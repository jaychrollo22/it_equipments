<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap reports-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Letter of Undertaking</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">All</a>
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
                            <div class="col-md-12">
                                <h4>Employee Details</h4>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search by Name" v-model="keywords">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="kt_datatable">
                                            <thead>
                                                <tr>
                                                    <th class="text-left">Employee</th>
                                                    <th class="text-left">Inventory Item Details</th>
                                                    <th class="text-center">Generate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, i) in filteredQueues" :key="i" >
                                                    <td align="left"><small>{{item.first_name + ' ' + item.last_name}}</small></td>
                                                    <td align="left">
                                                        <ul v-if="item.borrowed_items.length > 0">
                                                            <li v-for="(b_item, x) in item.borrowed_items" :key="x">
                                                                <small v-if="b_item.inventory_info">ID : {{b_item.inventory_info.id}}  
                                                                    | Serial No. : {{b_item.inventory_info.serial_number}}  
                                                                    | Model : {{b_item.inventory_info.model}}  
                                                                    | Type : {{b_item.inventory_info.type}}  
                                                                    | Processor : {{b_item.inventory_info.processor}}  
                                                                    | OS and Version : {{b_item.inventory_info.os_name_and_version}}  
                                                                </small>
                                                            </li>
                                                        </ul>
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <a :href="'/reports-letter-of-undertaking-print?id='+item.id" class="btn btn-primary">Generate</a>
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
                                        <span class="mr-2">Total : {{ filteredLetterOfUndertakings.length }} </span><br>
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
                letterOfUndertakings: [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,
                selectedItem : '', 
            }
        },
        created () {
            this.getLetterOfUndertakings();
        },
        methods: {
            getLetterOfUndertakings() {
                let v = this;
                v.letterOfUndertakings = [];
                axios.get('/reports-letter-of-undertaking-data')
                .then(response => { 
                    v.letterOfUndertakings = response.data;
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
            filteredLetterOfUndertakings(){
                let self = this;
                return Object.values(self.letterOfUndertakings).filter(item => {
                    // return item;
                    if(item.borrowed_items.length > 0){
                        let full_name = item.first_name + ' ' +  item.last_name;
                        return item.first_name.toLowerCase().includes(this.keywords.toLowerCase())
                                || item.last_name.toLowerCase().includes(this.keywords.toLowerCase())
                                || full_name.toLowerCase().includes(this.keywords.toLowerCase())
                              
                    }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredLetterOfUndertakings).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredLetterOfUndertakings.slice(index, index + this.itemsPerPage);

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