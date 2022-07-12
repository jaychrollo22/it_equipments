<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap reports-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Asset History</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Timeline</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            
            <div class="container reports-container">
                <div class="card card-custom gutter-b">
                    

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h4>Inventory Details</h4>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search by ID | Serial No. | Model | Type | Location" v-model="keywords">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="kt_datatable">
                                            <thead>
                                                <tr>
                                                    <th class="text-left">ID</th>
                                                    <th class="text-left">Serial No.</th>
                                                    <th class="text-left">Model</th>
                                                    <th class="text-left">Type</th>
                                                    <th class="text-left">Location</th>
                                                    <th class="text-center">Total Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, i) in filteredQueues" :key="i" >
                                                    <td align="left">
                                                        <small @click="viewTimeline(item)">
                                                            <u style="cursor:pointer;" class="text-primary">{{item.id}}</u>
                                                        </small>
                                                    </td>
                                                    <td align="left"><small>{{item.serial_number}}</small></td>
                                                    <td align="left"><small>{{item.model}}</small></td>
                                                    <td align="left"><small>{{item.type}}</small></td>
                                                    <td align="left"><small>{{item.location}}</small></td>
                                                    <td align="center">
                                                        <strong style="cursor:pointer;" class="text-primary" @click="viewTimeline(item)">{{item.user_inventories.length}}</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                
                                <div class="row col-md-12" v-if="filteredQueues.length">
                                    <div class="col-6">
                                        <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                            <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                        <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="mr-2">Total Asset : {{ filteredAssetUserHistory.length }} </span><br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" v-if="selectedItem">
                                <h4 class="text-center">Timeline</h4>
                                <h4 class="text-center">{{selectedItem.id}}</h4>
                                <center>
                                    <small class="text-center">{{selectedItem.serial_number}}</small>
                                </center>
                                
                                <div class="timeline timeline-3 mt-5">
                                    <div class="timeline-items">
                                        <div class="timeline-item" v-for="(item, i) in selectedItem.user_inventories" :key="i">
                                            <div class="timeline-media">
                                                <i class="flaticon2-user text-success"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <div class="mr-2">
                                                        <a href="#" class="text-dark-75 text-hover-primary font-weight-bold">{{item.employee_info.first_name + ' ' + item.employee_info.last_name}}</a> <br>
                                                        <small class="text-muted ml-2">{{item.employee_info.cluster}}</small> <br>
                                                        <span v-if="item.is_assigned == 'true'" class="label label-light-primary font-weight-bolder label-inline ml-2 mt-2">Assigned</span>
                                                        <span v-else class="label label-light-warning font-weight-bolder label-inline ml-2 mt-2">Borrowed</span>
                                                    </div>
                                                </div>
                                                <ul>
                                                    <li v-if="item.return_date">Return Date: {{item.return_date}}</li>
                                                    <li>Borrow Date: {{item.borrow_date}}</li>
                                                    <li>Ticket No.: {{item.ticket_number}}</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                assetUserHistory: [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,
                selectedItem : '', 
            }
        },
        created () {
            this.getAssetUserHistory();
        },
        methods: {
            viewTimeline(item){
                this.selectedItem = item;
            },
            getAssetUserHistory() {
                let v = this;
                v.assetUserHistory = [];
                axios.get('/reports-asset-user-history-data')
                .then(response => { 
                    v.assetUserHistory = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            setPage(pageNumber) {
                this.currentPage = pageNumber;
            },
            resetStartRow() {
                this.currentPage = 0;
            },
            showPreviousLink() {
                return this.currentPage == 0 ? false : true;
            },
            showNextLink() {
                return this.currentPage == (this.totalPages - 1) ? false : true;
            } 
        },
        computed:{
            filteredAssetUserHistory(){
                let self = this;
                return Object.values(self.assetUserHistory).filter(item => {
                    // return item;
                    // if(item.employee_info && item.inventory_info){
                        // let full_name = item.employee_info.first_name + ' ' +  item.employee_info.last_name;
                        return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.type.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.model.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.location.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.id == this.keywords
                    // }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredAssetUserHistory).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredAssetUserHistory.slice(index, index + this.itemsPerPage);

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
        .reports-container{
            max-width: 1840px!important;
        }
    }
</style>