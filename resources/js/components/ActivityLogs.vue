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
                        <h2 class="text-white font-weight-bold my-2 mr-5">Activity Logs</h2>
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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Logs</a>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Lists</a>
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
                            <h3 class="card-label">Activity Logs
                            <span class="d-block text-muted pt-2 font-size-sm">Employee Activities/Logs</span></h3>
                        </div>
                    </div>

                     <div class="card-body">

                         <!-- FIlter -->
                         <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="filter-from">Date From</label>
                                    <input id="filter-from" type="date" class="form-control" v-model="filter.startDate">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="filter-from">Date To</label>
                                    <input id="filter-to" type="date" class="form-control" v-model="filter.endDate">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-sm btn-primary" @click="getActivityLogs">Apply Filter</button>
                            </div>
                        </div>

                        <!--begin: Datatable-->
                        <div class="table-responsive">
                                <table class="table table-bordered table-checkable" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Event</th>
                                            <th>Name</th>
                                            <th>Model</th>
                                            <th>ID</th>
                                            <th>Description</th>
                                            <th>Log Date</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <tr v-for="(activity_log, u) in filteredQueues" v-bind:key="u">
                                            <td>{{ activity_log.user_id }}</td>
                                            <td>{{ activity_log.event }}</td>
                                            <td>{{ activity_log.user.name }}</td>
                                            <td>{{ activity_log.auditable_type }}</td> 
                                            <td>{{ activity_log.auditable_id }}</td>  
                                            <td>{{ activity_log.ip_address }}</td>  
                                            <td>{{ activity_log.created_at }}</td>  
                                        </tr>
                                    </tbody>
                                </table>
                        </div>
                        <!--end: Datatable-->
                        <div class="row col-md-12" v-if="filteredActivityLogs.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPageActivityLog - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPageActivityLog + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPageActivityLog + 1)"> Next </button>
                            </div>
                        </div>

                        <!-- <div class="row" v-if="filteredActivityLogs.length == 0">
                            <div class="col-md-3 pt-5 pb-5">
                                <div class="spinner spinner-left spinner-primary spinner-lg">
                                    <span class="ml-15">Loading.. Please wait..</span>
                                </div>
                            </div>
                        </div> -->

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
                searhKeyword : '',
                filter: {
                    'startDate' : '',
                    'endDate' : ''
                },
                activityLogs : [],
                errors : [],
                currentPageActivityLog: 0,
                itemsPerPageActivityLog: 10, 
            }
        },
        created () {
            this.getActivityLogs();
        },
        methods: {
            getActivityLogs() {
                let v = this;
                v.activityLogs = [];
                var startDate = v.filter.startDate ? v.filter.startDate : "";
                var endDate = v.filter.endDate ? v.filter.endDate : "";
                 axios.get('/get-activity-logs?startDate='+startDate+'&endDate='+endDate)
                .then(response => { 
                    v.activityLogs = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },

            setPage(pageNumber) {
                this.currentPageActivityLog = pageNumber;
            },
            resetStartRow() {
                this.currentPageActivityLog = 0;
            },
            showPreviousLink() {
                return this.currentPageActivityLog == 0 ? false : true;
            },
            showNextLink() {
                return this.currentPageActivityLog == (this.totalPagesQuarterPeriod - 1) ? false : true;
            } 
        },
        computed:{
            //Quarter Periods
            filteredActivityLogs(){
                let self = this;
                return Object.values(self.activityLogs).filter(activity => {
                    return activity.event.toLowerCase().includes(this.searhKeyword.toLowerCase()) || activity.user.name.toLowerCase().includes(this.searhKeyword.toLowerCase()) 
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredActivityLogs).length / this.itemsPerPageActivityLog)
            },
            filteredQueues() {
                var index = this.currentPageActivityLog * this.itemsPerPageActivityLog;
                var queues_array = this.filteredActivityLogs.slice(index, index + this.itemsPerPageActivityLog);

                if(this.currentPageActivityLog >= this.totalPages) {
                    this.currentPageActivityLog = this.totalPages - 1
                }

                if(this.currentPageActivityLog == -1) {
                    this.currentPageActivityLog = 0;
                }

                return queues_array;
            },
        }
    }
</script>

<style lang="scss" scoped>

</style>