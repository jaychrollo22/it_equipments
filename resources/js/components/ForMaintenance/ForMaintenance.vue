<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">For Maintenance</h2>
                            <div class="d-flex align-items-center font-weight-bold my-2">
                                <a href="#" class="opacity-75 hover-opacity-100">
                                    <i class="flaticon2-shelter text-white icon-1x"></i>
                                </a>
                                <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                                <!-- <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Select Items for Maintenance</a> -->
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="getForMaintenance" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <!--begin::Container-->
                <div class="container inventories-container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">List
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <!-- <button class="btn btn-primary mr-2" @click="receiveItems">Receive Items</button> -->
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search by ID | Serial No. | Model | Type | Location" v-model="keywords">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Filter Status</label>
                                        <select class="form-control" v-model="filter_status">
                                            <option value="">All</option>
                                            <option value="For Maintenance">For Maintenance</option>
                                            <option value="Done Maintenance">Done Maintenance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Serial Number</th>
                                            <th>Model</th>
                                            <th>Type</th>
                                            <th>Location</th>
                                            <th class="text-center">Created At</th>
                                            <th class="text-center">Schedule</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in filteredQueues" :key="i" >
                                            <td style="vertical-align: middle;"><small>{{item.inventory.id}}</small></td>
                                            <td style="vertical-align: middle;"><small>{{item.inventory.serial_number}}</small></td>
                                            <td style="vertical-align: middle;"><small>{{item.inventory.model}}</small></td>
                                            <td style="vertical-align: middle;"><small>{{item.inventory.type}}</small></td>
                                            <td style="vertical-align: middle;"><small>{{item.inventory.location}}</small></td>
                                            <td align="center" style="vertical-align: middle;"><small>{{item.created_at}}</small></td>
                                            <td align="center">
                                                <button v-if="item.maintenance_date && item.maintenance_date != '0000-00-00 00:00:00'" @click="setSchedule(item)" class="btn btn-outline-success btn-sm" >{{item.maintenance_date}}</button>
                                                <button v-else class="btn btn-outline-warning btn-sm" @click="setSchedule(item)">Set Schedule</button>
                                            </td>
                                            <td align="center">
                                                <button v-if="item.status == 'For Maintenance'" class="btn btn-outline-warning btn-sm" @click="changeStatus(item)">{{item.status}}</button>
                                                <button v-else class="btn btn-outline-primary btn-sm">{{item.status}}</button>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  

        <div class="modal fade" id="set-schedule-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content" v-if="for_maintenance">
                    <div>
                        <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header" >
                        <h2 class="col-12 modal-title text-center">Set Schedule ({{for_maintenance.inventory.id}})</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Schedule Date</label> 
                                    <input type="datetime-local" class="form-control" v-model="for_maintenance.maintenance_date">
                                    <span class="text-danger" v-if="errors.maintenance_date">{{ errors.maintenance_date[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary mr-3" @click="saveSetSchedule" :disabled="saveDisable">Set Schedule</button>
                        <button class="btn btn-danger" data-dismiss="modal" >Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="change-status-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content" v-if="for_maintenance">
                    <div>
                        <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header" >
                        <h2 class="col-12 modal-title text-center">Change Status ({{for_maintenance.inventory.id}})</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Status</label> 
                                    <select class="form-control" v-model="for_maintenance.status">
                                        <option value="Done Maintenance">Done Maintenance</option>
                                        <option value="For Maintenance">For Maintenance</option>
                                    </select>
                                    <span class="text-danger" v-if="errors.status">{{ errors.status[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Remarks</label> 
                                    <textarea class="form-control" v-model="remarks" cols="30" rows="5" placeholder="Remarks Maintenance"></textarea>
                                    <span class="text-danger" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary mr-3" @click="saveChangeStatus" :disabled="saveDisable">Change Status</button>
                        <button class="btn btn-danger" data-dismiss="modal" >Close</button>
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
                filter_status: '',
                for_maintenances : [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,

                for_maintenance : '',
                saveDisable : false,
            }
        },
        created () {
            this.getForMaintenance();
        },
        methods: {
            saveChangeStatus(){
                let v = this;
                v.saveDisable = true;
                Swal.fire({
                title: 'Are you sure you want change status?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.for_maintenance.id);
                        formData.append('status', v.for_maintenance.status);
                        formData.append('remarks', v.for_maintenance.remarks);
                        axios.post(`/for-maintenance-change-status`, formData)
                        .then(response =>{
                            if(response.data.status=='success'){
                                Swal.fire('For maintenance has been saved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            var index = this.for_maintenances.findIndex(item => item.id == v.for_maintenance.id);
                                                this.for_maintenances.splice(index,1,response.data.for_maintenance);
                                            $('#change-status-modal').modal('hide');
                                            this.for_maintenance = '';
                                            v.saveDisable = false;
                                        }
                                    });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                            v.saveDisable = false;
                        })

                    }else{
                         v.saveDisable = false;
                    }
                })
            },
            changeStatus(item){
                this.for_maintenance = Object.assign({},item);
                $('#change-status-modal').modal('show');
            },
            saveSetSchedule(){
                let v = this;
                v.saveDisable = true;
                Swal.fire({
                title: 'Are you sure you want set schedule for this item?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.for_maintenance.id);
                        formData.append('maintenance_date', v.for_maintenance.maintenance_date);
                        axios.post(`/for-maintenance-set-schedule`, formData)
                        .then(response =>{
                            if(response.data.status=='success'){
                                Swal.fire('For maintenance has been saved. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            var index = this.for_maintenances.findIndex(item => item.id == v.for_maintenance.id);
                                                this.for_maintenances.splice(index,1,response.data.for_maintenance);
                                            $('#set-schedule-modal').modal('hide');
                                            this.for_maintenance = '';
                                            v.saveDisable = false;
                                        }
                                    });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                            v.saveDisable = false;
                        })

                    }else{
                         v.saveDisable = false;
                    }
                })
            },
            setSchedule(item){
                this.for_maintenance = Object.assign({},item);
                if(item.maintenance_date){
                    var date = moment(item.maintenance_date).format('YYYY-MM-DDTHH:mm:ss');
                    this.for_maintenance.maintenance_date = date;
                }
                $('#set-schedule-modal').modal('show');
            },
            getForMaintenance() {
                let v  = this;
                v.for_maintenances = [];
                axios.get('/for-maintenance-data')
                .then(response => { 
                    v.for_maintenances = response.data;
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
            filterForMaintenances(){
                let self = this;
                if(self.for_maintenances){
                    self.resetStartRowUser();
                    return Object.values(self.for_maintenances).filter(item => {
                        if(self.filter_status){
                            if(self.filter_status == item.status){
                                return item.inventory.model.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.type.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.location.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.id == this.keywords
                            }
                        }else{
                            if(item.inventory){
                                return item.inventory.model.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.type.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.location.toLowerCase().includes(this.keywords.toLowerCase())
                                        || item.inventory.id == this.keywords
                            }
                        }
                        
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filterForMaintenances).length / Number(this.itemsPerPage))
            },
            filteredQueues() {
                var index = this.currentPage * Number(this.itemsPerPage);
                var queues_array = this.filterForMaintenances.slice(index, index + Number(this.itemsPerPage));
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
    @media (min-width: 1400px){
        .inventories-container{
            max-width: 1840px!important;
        }
    }
</style>