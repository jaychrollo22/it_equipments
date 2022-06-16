<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">Assigned/Borrowed</h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="getHomeUserData" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
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
                            <table class="table table-checkable table-bordered" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Ticket No.</th>
                                        <th class="text-center">Item Details</th>
                                        <th class="text-center">Borrow Date</th>
                                        <th class="text-center">Return Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredQueues" :key="i" >
                                       <td align="center"><small>{{item.ticket_number}}</small></td>
                                       <td>
                                           <small>Model : {{item.inventory_info.model}}</small><br>
                                           <small>Serial No. : {{item.inventory_info.serial_number}}</small><br>
                                           <small>Type : {{item.inventory_info.type}}</small>
                                        </td>
                                        <td align="center"><small>{{item.borrow_date}}</small></td>
                                        <td align="center"><small>{{item.return_date}}</small></td>
                                        <td align="center"><small>{{item.status}}</small></td>
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
                                <span class="mr-2">Total : {{ filteredHomeUserData.length }} </span><br>
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
                homeUserData: [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10, 
            }
        },
        created () {
            this.getHomeUserData();
        },
        methods: {
            getHomeUserData() {
                let v = this;
                v.homeUserData = [];
                axios.get('/home-user-data')
                .then(response => { 
                    v.homeUserData = response.data;
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
            filteredHomeUserData(){
                let self = this;
                if(self.homeUserData){
                    return Object.values(self.homeUserData).filter(item => {
                        return item.inventory_info.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.inventory_info.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.inventory_info.type.toLowerCase().includes(this.keywords.toLowerCase())
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredHomeUserData).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredHomeUserData.slice(index, index + this.itemsPerPage);
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