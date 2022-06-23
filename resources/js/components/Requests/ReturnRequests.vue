<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">All Return Requests</h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="refresh" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
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
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search Request No. | Ticket No. | Details | Location" v-model="keywords">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-checkable table-bordered" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Request No.</th>
                                            <th class="text-center">Request Details</th>
                                            <th class="text-center">Items</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in filteredQueues" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;">
                                                <small>{{item.request_number}}</small>
                                            </td>
                                            <td align="center">
                                                <tr>
                                                    <td align="left"><small><strong>Return Date</strong> </small></td>
                                                    <td align="left"><small>{{item.return_date}} </small></td>
                                                    <td align="left"><small><strong>Requested By</strong> </small></td>
                                                    <td align="left"><small>{{item.user.name}}</small></td>
                                                </tr>  
                                                <tr>  
                                                    <td align="left"><small><strong>Details</strong> </small></td>
                                                    <td align="left"><small>{{item.details}} </small></td>
                                                    <td align="left"><small><strong>Status</strong> </small></td>
                                                    <td align="left"><small>{{item.status}}</small></td>
                                                </tr>

                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="view(item)" title="View Items"><strong>{{item.return_request_items.length}}</strong></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                             <div class="row col-md-12" v-if="filteredQueues.length">
                                <div class="col-6">
                                    <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                        <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                    <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                                </div>
                                <div class="col-6 text-right">
                                    <span class="mr-2">Total : {{ filteredReturnRequestData.length }} </span><br>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" id="return-items-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">Return Items ({{request.request_number}})</h2>
                    </div>
                    <div class="modal-body"  v-if="return_items" >
                        <div class="table-responsive">
                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <thead>
                                    <th>Item Details</th>
                                    <th>Check</th>
                                    <th>Remarks</th>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in return_items" :key="i" >
                                        <td>
                                            <small>ID : {{item.user_inventory.inventory_info.id}}</small><br>
                                            <small>Model : {{item.user_inventory.inventory_info.model}}</small><br>
                                            <small>Serial Number : {{item.user_inventory.inventory_info.serial_number}}</small><br>
                                            <small>Type : {{item.user_inventory.inventory_info.type}}</small><br>
                                        </td>
                                        <td>
                                            <select class="form-control" v-model="item.check_status">
                                                <option value="Good">Good</option>
                                                <option value="With Defects">With Defects</option>
                                            </select>
                                            <br>
                                            <div v-if="item.check_status == 'With Defects'">
                                                <small for="">Defect Value Deduction</small><br>
                                                <input type="number" class="form-control" v-model="item.defect_value_deduction">
                                            </div>
                                           
                                        </td>
                                        <td>
                                            <textarea class="form-control" v-model="item.check_remarks" cols="30" rows="2" placeholder="Remarks"></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="request.status == 'For Checking'" class="btn btn-primary btn-md" @click="acceptCheck" :disabled="acceptDisable">Accept & Receive</button>
                        <button v-else disabled class="btn btn-primary btn-md">Accept & Receive</button>
                        <button class="btn btn-danger btn-md" data-dismiss="modal" aria-label="Close">Close</button>
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
                borrowRequestsData: [],
                request : '',
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,
                
                acceptDisable : false,
                return_items : [],
            }
        },
        created () {
            this.getReturnRequestsData();
        },
        methods: {
            acceptCheck(){
               let v = this;
               v.acceptDisable = true;
                Swal.fire({
                title: 'Are you sure you want to accept and receive this return request?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.request.id ? v.request.id : "");
                        formData.append('return_items', v.return_items ? JSON.stringify(v.return_items) : "");
                        axios.post(`/return-request-accept`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Success: Request has been accepted. Thank you.', '', 'success'); 
                                this.getReturnRequestsData();
                                v.acceptDisable = false;
                                 $('#return-items-modal').modal('hide');
                            }else{
                                Swal.fire('Error: Cannot delete. Please try again.', '', 'error'); 
                                v.acceptDisable = false;
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                            v.acceptDisable = false;
                        })

                    }else{
                        v.acceptDisable = false;
                    }
                })

            },
            view(item){
                let v = this;
                v.request = item;
                v.return_items = item.return_request_items;
                $('#return-items-modal').modal('show');
            },
            refresh(){
                window.location.href = '/return-requests';
            },
            getReturnRequestsData() {
                let v = this;
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                var request_number = urlParams.get('request_number');
                if(request_number){
                    v.keywords = request_number;
                }

                v.borrowRequestsData = [];
                axios.get('/return-requests-data')
                .then(response => { 
                    v.borrowRequestsData = response.data;
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
            filteredReturnRequestData(){
                let self = this;
                if(self.borrowRequestsData){
                    return Object.values(self.borrowRequestsData).filter(item => {
                        return item.details.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.request_number.toLowerCase().includes(this.keywords.toLowerCase())            
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredReturnRequestData).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredReturnRequestData.slice(index, index + this.itemsPerPage);
                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }
                if(this.currentPage == -1) {
                    this.currentPage = 0;
                }
                return queues_array;
            },
        }
    }
</script>

<style lang="scss" scoped>

</style>