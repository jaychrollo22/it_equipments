<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">For Disposal</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Select Items for Disposal</a>
                        </div>
                    </div>
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
                                        <th class="text-center">Requested Date</th>
                                        <th class="text-center">Requested By</th>
                                        <th class="text-center">Items For Disposal</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredForDisposalQueues" :key="i" >
                                        <td align="center">
                                            {{item.requested_date}}
                                        </td>
                                        <td align="center">
                                            {{item.requested_by_info.name}}
                                        </td>
                                        <td align="center">
                                            <a v-if="item.items.length > 0" :href="'/for-disposal-items?id=' + item.id" target='_blank'>
                                                 {{item.items.length}}
                                            </a>
                                        </td>
                                        <td align="center">
                                            <a  :href="'/for-disposal-items?id=' + item.id" target='_blank' class="label label-warning label-pill label-inline mr-2" style="cursor:pointer"> {{item.status}} </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                         <div class="row col-md-12" v-if="filteredForDisposalQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <!-- <div class="col-6 text-right">
                                <span class="mr-2">Total Inventories : {{ filteredInventories.length }} </span><br>
                            </div> -->
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
                keywords: '',
                for_disposals : [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,
            }
        },
        created () {
            this.getForDisposal();
        },
        methods: {
            getForDisposal() {
                let v  = this;
                axios.get('/for-disposal-data')
                .then(response => { 
                    v.for_disposals = response.data;
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
            filterForDisposals(){
                let self = this;
                if(self.for_disposals){
                    self.resetStartRowUser();
                    return Object.values(self.for_disposals).filter(item => {
                        if(item.requested_by_info){
                            return item.requested_by_info.name.toLowerCase().includes(this.keywords.toLowerCase())
                        }
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filterForDisposals).length / Number(this.itemsPerPage))
            },
            filteredForDisposalQueues() {
                var index = this.currentPage * Number(this.itemsPerPage);
                var queues_array = this.filterForDisposals.slice(index, index + Number(this.itemsPerPage));
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