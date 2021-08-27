<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column">
                        <!--begin::Title-->
                        <h2 class="text-white font-weight-bold my-2 mr-5">Departments</h2>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <!--begin::Item-->
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Configuration Settings</a>
                            <!--end::Item-->
                        </div>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Heading-->
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">List
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-success mr-2" @click="addDepartment">New</button>
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
                        <!--begin: Datatable-->
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">DEPARTMENT</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredDepartmentQueues" :key="i" >
                                       <td align="center"><small>{{item.name}}</small></td>
                                       <td align="center">
                                           <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editDepartment(item)">
                                                <i class="flaticon-edit"></i>
                                            </button>
                                       </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row col-md-12" v-if="filteredDepartmentQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Locations : {{ filteredDepartments.length }} </span><br>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>  
        </div>  
    </div>

    <div class="modal fade" id="department-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center"></h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">{{ action }} Department</label> 
                                <input type="text" class="form-control" placeholder="Input here..." v-model="department.name">
                                <span class="text-danger" v-if="errors.name">{{ errors.name[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveDepartment">Save</button>
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
            keywords: '',
            department : {
                'id' : '',
                'name' : ''
            },
            action : '',
            departments : [],
            errors : [],
            currentPage: 0,
            itemsPerPage: 10, 
        }
    },
    created () {
        this.getDepartments();
    },
    methods : {
        addDepartment(){
            let v = this;
            v.errors = [];
            v.department.id = '';
            v.department.name = '';
            v.action = 'New';
            $('#department-modal').modal('show');
        },
        editDepartment(department){
            let v = this;
            v.errors = [];
            v.department.id = department.id;
            v.department.name = department.name;
            v.action = 'Update';
            $('#department-modal').modal('show');
        },
        saveDepartment(){
            let v = this;
            Swal.fire({
                title: 'Are you sure you want to save this department?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    var postURL = `/setting-departments-store`;
                    if(v.action == "Update"){
                        formData.append('id', v.department.id ? v.department.id : "");
                        postURL = `/setting-departments-update`;
                    }
                    formData.append('name', v.department.name ? v.department.name : "");
                    axios.post(postURL, formData)
                    .then(response =>{
                        if(response.data.status == "success"){
                            Swal.fire('Department has been saved!', '', 'success');        
                            $('#department-modal').modal('hide');
                            v.department.id = '';
                            v.department.name = '';  
                            this.getDepartments();
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
        getDepartments() {
            let v = this;
            v.departments = [];
            axios.get('/setting-departments-data')
            .then(response => { 
                v.departments = response.data;
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
        filteredDepartments(){
            let self = this;
            if(self.departments){
                return Object.values(self.departments).filter(item => {
                    return item.name.toLowerCase().includes(this.keywords.toLowerCase())
                });
            }else{
                return [];
            }
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredDepartments).length / this.itemsPerPage)
        },
        filteredDepartmentQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredDepartments.slice(index, index + this.itemsPerPage);
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