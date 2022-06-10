<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">System Approvers</h2>
                            <div class="d-flex align-items-center font-weight-bold my-2">
                                <a href="#" class="opacity-75 hover-opacity-100">
                                    <i class="flaticon2-shelter text-white icon-1x"></i>
                                </a>
                                <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                                <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Configuration Settings</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">List
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-success mr-2" @click="addSystemApprover">New</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Input here..." v-model="keywords">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-checkable" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">USER</th>
                                            <th class="text-center">TYPE</th>
                                            <th class="text-center">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in filteredQueues" :key="i" >
                                            <td align="center"><small>{{item.user.name}}</small></td>
                                            <td align="center">
                                                <small>{{item.approver_type}}</small>
                                            </td>
                                            <td align="center">
                                                <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editSystemApprover(item)">
                                                    <i class="flaticon-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="system-approver-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">{{action}}</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <select class="form-control" v-model="system_approver.user_id">
                                        <option value="">Choose Approver</option>
                                        <option v-for="(employee,b) in employees" v-bind:key="b" :value="employee.user_id"> {{ employee.first_name + ' ' + employee.last_name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <select class="form-control" v-model="system_approver.approver_type">
                                        <option value="">Choose Type</option>    
                                        <option value="IT">IT</option>    
                                        <option value="FINANCE">FINANCE</option>    
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-md" @click="saveSystemApprover">Save</button>
                    </div>
                </div>
            </div>
        </div>



    </div>
</template>

<script>
    export default {
        data() {
            return {
                keywords : '',
                system_approver: {
                    id : '',
                    user_id : '',
                    approver_type : ''
                },
                systemApprovers: [],
                action : '',
                errors: [],
                currentPage: 0,
                itemsPerPage: 10, 
                employees : '',
            }
        },
        created () {
            this.getEmployees();
            this.getSystemApprovers();
        },
        methods: {
            getEmployees(){
                let v = this;
                v.employees = '';
                axios.get('/all-employees')
                .then(response => { 
                    v.employees = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            saveSystemApprover(){
                let v = this;
                Swal.fire({
                    title: 'Are you sure you want to save this system approver?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        var postURL = `/setting-system-approvers-store`;
                        if(v.action == "Update"){
                            formData.append('id', v.system_approver.id ? v.system_approver.id : "");
                            postURL = `/setting-system-approvers-update`;
                        }
                        formData.append('user_id', v.system_approver.user_id ? v.system_approver.user_id : "");
                        formData.append('approver_type', v.system_approver.approver_type ? v.system_approver.approver_type : "");
                        axios.post(postURL, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('System Approver has been saved!', '', 'success');        
                                $('#system-approver-modal').modal('hide');
                                v.system_approver.id = '';
                                v.system_approver.user_id = '';  
                                v.system_approver.approver_type = '';  
                                this.getSystemApprovers();
                            }else{
                                Swal.fire('Error: Cannot changed. Please try again.', '', 'error');   
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        })
                    }   
                })
            },
            addSystemApprover() {
                let v = this;
                v.errors = [];
                v.system_approver.user_id = '';
                v.system_approver.approver_type = '';
                v.action = 'New';
                $('#system-approver-modal').modal('show');
            },
            editSystemApprover(item){
                let v = this;
                v.errors = [];
                v.system_approver.id = item.id;
                v.system_approver.user_id = item.user_id;
                v.system_approver.approver_type = item.approver_type;
                v.action = 'Update';
                $('#system-approver-modal').modal('show');
            },
            getSystemApprovers() {
                let v = this;
                v.systemApprovers = [];
                axios.get('/setting-system-approvers-data')
                .then(response => { 
                    v.systemApprovers = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            setPage(pageNumber) {
                this.currentPage = pageNumber;
            },
            resetStartRowUser() {
                this.currentPage = 0;
            },
            showPreviousLink() {
                return this.currentPage == 0 ? false : true;
            },
            showNextLink() {
                return this.currentPage == (this.totalPages - 1) ? false : true;
            },
        },
        computed: {
            filteredSystemApprovers(){
                let self = this;
                if(self.systemApprovers){
                    return Object.values(self.systemApprovers).filter(item => {
                        return item.user.name.toLowerCase().includes(this.keywords.toLowerCase())
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredSystemApprovers).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredSystemApprovers.slice(index, index + this.itemsPerPage);
                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }
                if(this.currentPage == -1) {
                    this.currentPage = 0;
                }
                return queues_array;
            },
        },
    }
</script>

<style lang="scss" scoped>

</style>