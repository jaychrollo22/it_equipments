<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">User Return Requests</h2>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="#" @click="refresh" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="container inventories-container">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">List
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-primary mr-2 mt-1" @click="newRequest">New</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search Request No. | Details" v-model="keywords">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-checkable table-bordered" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th>Request Details</th>
                                            <th class="text-center">Items to Return</th>
                                            <th class="text-center">Location</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in filteredQueues" :key="i" >
                                            <td style="vertical-align: middle;">
                                                <small>Request No. : {{item.request_number}}</small><br>
                                                <small>Details : {{item.details}}</small><br>
                                                <small>Date : {{item.return_date}}</small><br>
                                                <small>Status : {{item.status}}</small>
                                            </td>
                                            <td align="center">
                                                <tr >
                                                    <td width="100"><small><strong>ID</strong> </small> </td>
                                                    <td width="100"><small><strong>TYPE</strong> </small> </td>
                                                    <td width="200"><small><strong>MODEL</strong> </small> </td>
                                                    <td width="150"><small><strong>SERIAL NO.</strong> </small> </td>
                                                    <td width="150"><small><strong>STATUS</strong> </small> </td>
                                                    <td width="150"><small><strong>REMOVE</strong> </small> </td>
                                                </tr>
                                                 <tr v-for="(item_return, i) in item.return_request_items" :key="i"  >
                                                    <td><small>{{item_return.user_inventory.inventory_info.id}}</small> </td>
                                                    <td><small>{{item_return.user_inventory.inventory_info.type}}</small> </td>
                                                    <td><small>{{item_return.user_inventory.inventory_info.model}}</small> </td>
                                                    <td><small>{{item_return.user_inventory.inventory_info.serial_number}}</small> </td>
                                                    <td>
                                                        <label :class="getColorStatus(item_return.status)">{{item_return.status}}</label>
                                                    </td>
                                                    <td>
                                                        <button v-if="item_return.status == 'For Checking' && item.return_request_items.length > 1" type="button" class="btn btn-light-danger btn-icon btn-sm" @click="deleteRequestItem(item,item_return)" title="Delete Item"><i class="flaticon-cancel"></i></button>
                                                    </td>
                                                </tr>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <small>{{item.location}}</small><br>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button v-if="item.status == 'For Checking'" type="button" class="btn btn-light-danger btn-icon btn-sm" @click="deleteRequest(item)" title="Delete Request"><i class="flaticon-delete"></i></button>
                                                <button v-else disabled type="button" class="btn btn-light-danger btn-icon btn-sm" @click="deleteRequest(item)"><i class="flaticon-delete"></i></button>
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

        <div class="modal fade" id="return-request-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div> 
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">{{action}} Request</h2>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" placeholder="Search (ID/TYPE/MODEL/SERIAL NUMBER)" v-model="keyword_borrowed_item">
                                    </div>
                                </div>
                            <div class="col-md-12">
                                <h4>Borrowed Items</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th>Model</th>
                                            <th>Serial No.</th>
                                            <th>Location</th>
                                            <th>Validity Date</th>
                                            <th>Action</th>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in filteredQueuesBorrowedItems" :key="i" >
                                                <td><small>{{item.inventory_info.id}}</small></td>
                                                <td><small>{{item.inventory_info.type}}</small></td>
                                                <td><small>{{item.inventory_info.model}}</small></td>
                                                <td><small>{{item.inventory_info.serial_number}}</small></td>
                                                <td><small>{{item.inventory_info.location}}</small></td>
                                                <td><small>{{item.validity_end_date}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <button v-if="validateBorrowItem(item.inventory_info)" class="btn btn-light-primary btn-icon btn-sm" @click="addBorrowItem(item)"><i class="flaticon-plus"></i></button>
                                                    <button v-else class="btn btn-light-primary btn-icon btn-sm" disabled><i class="flaticon-plus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                                <div class="row col-md-12" v-if="filteredBorrowedItems.length">
                                    <div class="col-6">
                                        <button :disabled="!showPreviousLinkBorrowedItem()" class="btn btn-default btn-sm btn-fill" v-on:click="setPageBorrowedItem(currentPageBorrowedItem - 1)"> Previous </button>
                                            <span class="text-dark">Page {{ currentPageBorrowedItem + 1 }} of {{ totalPagesBorrowedItem }}</span>
                                        <button :disabled="!showNextLinkBorrowedItem()" class="btn btn-default btn-sm btn-fill" v-on:click="setPageBorrowedItem(currentPageBorrowedItem + 1)"> Next </button>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="mr-2">Total : {{ filteredBorrowedItems.length }} </span><br>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2" v-if="selectedItems.length > 0">
                                <h5>Selected Items to Return ({{selectedItems.length}})</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-checkable" id="kt_datatable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Type</th>
                                                <th>Model</th>
                                                <th>Serial No.</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in selectedItems" :key="i" >
                                                <td><small>{{item.inventory_info.id}}</small></td>
                                                <td><small>{{item.inventory_info.type}}</small></td>
                                                <td><small>{{item.inventory_info.model}}</small></td>
                                                <td><small>{{item.inventory_info.serial_number}}</small></td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <button type="button" class="btn btn-light-danger btn-icon btn-sm" @click="removeBorrowItem(item)"><i class="flaticon-cancel"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label for="role">Remarks/Details</label> 
                                    <textarea class="form-control" cols="20" rows="5" placeholder="Input Remarks/Details" v-model="request.details"></textarea>
                                    <span class="text-danger" v-if="errors.details">{{ errors.details[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label for="role">Location</label> 
                                    <select class="form-control" v-model="request.location">
                                        <option value="">Choose</option>
                                        <option v-for="(location,b) in locations" v-bind:key="b" :value="location.name"> {{ location.name }}</option>
                                    </select>
                                    <span class="text-danger" v-if="errors.location">{{ errors.location[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-md" @click="save" :disabled="saveDisable">{{saveDisableLabel}}</button>
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
                keywords: '',
                request: {
                    'id' : '',
                    'details' : '',
                },
                returnRequestsData: [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10,
            
                saveDisable : false,
                saveDisableLabel : 'Save',

                keyword_borrowed_item: '',
                currentPageBorrowedItem: 0,
                itemsPerPageBorrowedItem: 5,
                borrowed_items : [],

                action : '',

                selectedItems : [],
                locations : [],
            }
        },
        created () {
            this.getReturnRequestsData();
            this.getBorrowedItems();
            this.getLocations();
        },
        methods: {
            getLocations() {
                let v = this;
                v.locations = [];
                axios.get('/setting-locations-data')
                .then(response => { 
                    v.locations = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            getColorStatus(item){
                if(item == 'For Checking'){
                    return 'label label-warning label-pill label-inline mr-2';
                }else if(item == 'Checked'){
                    return 'label label-success label-pill label-inline mr-2';
                }else{
                    return 'label label-default label-pill label-inline mr-2';
                }
            },
            refresh(){
                window.location.href = '/home-return-requests';
            },
            deleteRequest(item){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this request? (' + item.request_number + ')',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', item.id ? item.id : "");
                        axios.post(`/return-request-delete`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Success: Request has been deleted. Thank you.', '', 'success'); 
                                this.getReturnRequestsData();
                                this.getBorrowedItems();
                            }else{
                                Swal.fire('Error: Cannot delete. Please try again.', '', 'error'); 
                            }
                        })

                    }
                })
            },
            deleteRequestItem(return_request,request_item){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to delete this item? ('+request_item.user_inventory.inventory_info.id+')',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', request_item.id ? request_item.id : "");
                        formData.append('return_request_id', return_request.id ? return_request.id : "");
                        axios.post(`/return-request-item-delete`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Success: Request has been deleted. Thank you.', '', 'success'); 
                                this.getReturnRequestsData();
                                this.getBorrowedItems();
                            }else{
                                Swal.fire('Error: Cannot delete. Please try again.', '', 'error'); 
                            }
                        })

                    }
                })
            },
            addBorrowItem(item){
                let v = this;
                v.selectedItems.push(item);
            },
            removeBorrowItem(item){
                let v = this;
                this.selectedItems.splice(item, 1);
            },
            validateBorrowItem(item){
                var hasMatch = this.selectedItems.filter(function (val) {
                    return !!(val.inventory_info.id === item.id);                               
                });
                if(hasMatch.length > 0){
                    return false;
                }else{
                    return true;
                }
            },
            save(){
                let v = this;
                v.saveDisable = true;
                Swal.fire({
                title: 'Are you sure you want to save this return request?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        v.saveDisableLabel = 'Saving...Please Wait...';
                        let formData = new FormData();
                        formData.append('details', v.request.details ? v.request.details : "");
                        formData.append('return_request_items', v.selectedItems ? JSON.stringify(v.selectedItems) : "");
                        formData.append('location', v.request.location ? v.request.location : "");
                        axios.post(`/return-request-store`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Request has been saved!', '', 'success');  
                                $('#return-request-modal').modal('hide');   
                                v.saveDisable = false;
                                v.saveDisableLabel = 'Save';
                                this.getReturnRequestsData();
                                this.getBorrowedItems();
                            }else{
                                v.saveDisable = false;
                                v.saveDisableLabel = 'Save';
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                            v.saveDisable = false;
                            v.saveDisableLabel = 'Save';
                        })
                    }else{
                        v.saveDisable = false;
                        v.saveDisableLabel = 'Save';
                    }
                })
            },
            newRequest(){
                let v = this;
                v.selectedItems = [];
                v.errors = [];
                v.request.id = '';
                v.request.ticket_number = '';
                v.action = 'New';
                $('#return-request-modal').modal('show');
            },
            getBorrowedItems(){
                let v = this;
                v.borrowed_items = [];
                axios.get('/home-return-requests-assigned-items')
                .then(response => { 
                    v.borrowed_items = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            setPageBorrowedItem(pageNumberBorrowedItem) {
                this.currentPageBorrowedItem = pageNumberBorrowedItem;
            },
            resetStartRowUserBorrowedItem() {
                this.currentPageBorrowedItem = 0;
            },
            showPreviousLinkBorrowedItem() {
                return this.currentPageBorrowedItem == 0 ? false : true;
            },
            showNextLinkBorrowedItem() {
                return this.currentPageBorrowedItem == (this.totalPagesBorrowedItem - 1) ? false : true;
            },
            getReturnRequestsData(){
                let v = this;
                v.returnRequestsData = [];
                axios.get('/home-return-requests-data')
                .then(response => { 
                    v.returnRequestsData = response.data;
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
            filteredBorrowedItems(){
                let self = this;
                if(self.borrowed_items){
                    this.resetStartRowUserBorrowedItem();
                    return Object.values(self.borrowed_items).filter(item => {
                        return item.inventory_info.type.toLowerCase().includes(this.keyword_borrowed_item.toLowerCase()) 
                                || item.inventory_info.model.toLowerCase().includes(this.keyword_borrowed_item.toLowerCase())
                                || item.inventory_info.serial_number.toLowerCase().includes(this.keyword_borrowed_item.toLowerCase())
                                || item.inventory_info.id == this.keyword_borrowed_item
                                
                    });
                }else{
                    return [];
                }
            },
            totalPagesBorrowedItem() {
                return Math.ceil(Object.values(this.filteredBorrowedItems).length / this.itemsPerPageBorrowedItem)
            },
            filteredQueuesBorrowedItems() {
                var index = this.currentPageBorrowedItem * this.itemsPerPageBorrowedItem;
                var queues_array = this.filteredBorrowedItems.slice(index, index + this.itemsPerPageBorrowedItem);
                if(this.currentPageBorrowedItem >= this.totalPagesBorrowedItems) {
                    this.currentPageBorrowedItem = this.totalPagesBorrowedItems - 1
                }
                if(this.currentPageBorrowedItem == -1) {
                    this.currentPageBorrowedItem = 0;
                }
                return queues_array;
            },

            filteredReturnRequestData(){
                let self = this;
                if(self.returnRequestsData){
                    return Object.values(self.returnRequestsData).filter(item => {
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
    @media (min-width: 1400px){
        .inventories-container{
            max-width: 1840px!important;
        }
    }
</style>