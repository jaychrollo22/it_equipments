<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column">
                        <!--begin::Title-->
                        <h2 class="text-white font-weight-bold my-2 mr-5">Inventories</h2>
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
            <div class="container inventories-container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Masterlist
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-primary mr-2" @click="addInventory">New</button>
                            <button class="btn btn-success mr-2" @click="getInventories">Refresh</button>
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
                                        <th class="text-center">RFID</th>
                                        <th class="text-center">TYPE</th>
                                        <th class="text-center">SERIAL NUMBER</th>
                                        <th class="text-center">MODEL</th>
                                        <th class="text-center">MANUFACTURER</th>
                                        <th class="text-center">SUPPLIER</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredInventoryQueues" :key="i" >
                                       <td align="center"><small>{{item.rfid_code}}</small></td>
                                       <td align="center"><small>{{item.type}}</small></td>
                                       <td align="center"><small>{{item.serial_number}}</small></td>
                                       <td align="center"><small>{{item.model}}</small></td>
                                       <td align="center"><small>{{item.manufacturer}}</small></td>
                                       <td align="center"><small>{{item.supplier}}</small></td>
                                       
                                       <td align="center">
                                          <span v-if="item.is_borrowed" class="label label-warning label-pill label-inline mr-2" style="cursor:pointer" @click="viewBorrowItem(item)">{{ item.is_borrowed.status }}</span>
                                          <span v-else class="label label-success label-pill label-inline mr-2">Available</span>
                                        </td>
                                       <td align="center">
                                           <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editInventory(item)">
                                                <i class="flaticon-edit"></i>
                                            </button>
                                       </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row col-md-12" v-if="filteredInventoryQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Inventories : {{ filteredInventories.length }} </span><br>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>  
        </div> 
    </div>

    <div class="modal fade" id="inventory-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">{{ action }}</h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">RFID</label> 
                                <input type="text" class="form-control" placeholder="Scan RFID" v-model="inventory.rfid_code" readonly>
                                <span class="text-danger" v-if="errors.rfid_code">{{ errors.rfid_code[0] }}</span>
                            </div>
                        </div>
                    </div>
                    <h5>Inventory Information</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Type</label> 
                                <select class="form-control" v-model="inventory.type">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(type,b) in types" v-bind:key="b" :value="type.name"> {{ type.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.rfid_code">{{ errors.rfid_code[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Old Inventory Number</label> 
                                <input type="text" class="form-control" placeholder="Old Inventory Number" v-model="inventory.old_inventory_number">
                                <span class="text-danger" v-if="errors.old_inventory_number">{{ errors.old_inventory_number[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">New IT Tag / QR Code / BAR CODE</label> 
                                <input type="text" class="form-control" placeholder="New IT Tag / QR Code / BAR CODE" v-model="inventory.new_it_tag_qr_code_bar_code">
                                <span class="text-danger" v-if="errors.new_it_tag_qr_code_bar_code">{{ errors.new_it_tag_qr_code_bar_code[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Serial Number</label> 
                                <input type="text" class="form-control" placeholder="Serial Number" v-model="inventory.serial_number">
                                <span class="text-danger" v-if="errors.serial_number">{{ errors.serial_number[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Model</label> 
                                <input type="text" class="form-control" placeholder="Model" v-model="inventory.model">
                                <span class="text-danger" v-if="errors.model">{{ errors.model[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Processor</label> 
                                <input type="text" class="form-control" placeholder="Processor" v-model="inventory.processor">
                                <span class="text-danger" v-if="errors.processor">{{ errors.processor[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Manufacturer</label> 
                                <input type="text" class="form-control" placeholder="Manufacturer" v-model="inventory.manufacturer">
                                <span class="text-danger" v-if="errors.manufacturer">{{ errors.manufacturer[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Supplier</label> 
                                <input type="text" class="form-control" placeholder="Supplier" v-model="inventory.supplier">
                                <span class="text-danger" v-if="errors.supplier">{{ errors.supplier[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Purchase Company</label> 
                                <input type="text" class="form-control" placeholder="Purchase Company" v-model="inventory.purchase_company">
                                <span class="text-danger" v-if="errors.purchase_company">{{ errors.purchase_company[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Delivery Date</label> 
                                <input type="date" class="form-control" placeholder="Delivery Date" v-model="inventory.delivery_date">
                                <span class="text-danger" v-if="errors.delivery_date">{{ errors.delivery_date[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Order Number</label> 
                                <input type="text" class="form-control" placeholder="Order Number" v-model="inventory.order_number">
                                <span class="text-danger" v-if="errors.order_number">{{ errors.order_number[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Retired Date</label> 
                                <input type="date" class="form-control" placeholder="Retired Date" v-model="inventory.retired_date">
                                <span class="text-danger" v-if="errors.retired_date">{{ errors.retired_date[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Estimated Retirement Date</label> 
                                <input type="date" class="form-control" placeholder="Estimated Retirement Date" v-model="inventory.estimated_retirement_date">
                                <span class="text-danger" v-if="errors.estimated_retirement_date">{{ errors.estimated_retirement_date[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Warranty Period</label> 
                                <input type="text" class="form-control" placeholder="Warranty Period" v-model="inventory.warranty_period">
                                <span class="text-danger" v-if="errors.warranty_period">{{ errors.warranty_period[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Asset Code</label> 
                                <input type="text" class="form-control" placeholder="Asset Code" v-model="inventory.asset_code">
                                <span class="text-danger" v-if="errors.asset_code">{{ errors.asset_code[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Purchase Cost</label> 
                                <input type="number" class="form-control" placeholder="Purchase Cost" v-model="inventory.purchase_cost">
                                <span class="text-danger" v-if="errors.purchase_cost">{{ errors.purchase_cost[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Insurance Date</label> 
                                <input type="date" class="form-control" placeholder="Insurance Date" v-model="inventory.insurance_date">
                                <span class="text-danger" v-if="errors.insurance_date">{{ errors.insurance_date[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">OS Name and Version</label> 
                                <input type="text" class="form-control" placeholder="OS Name and Version" v-model="inventory.os_name_and_version">
                                <span class="text-danger" v-if="errors.os_name_and_version">{{ errors.os_name_and_version[0] }}</span>
                            </div>
                        </div>
                    </div>
                    <h5>Others</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Tab Name</label> 
                                <input type="text" class="form-control" placeholder="Tab Name" v-model="inventory.tab_name">
                                <span class="text-danger" v-if="errors.tab_name">{{ errors.tab_name[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Area</label> 
                                <input type="text" class="form-control" placeholder="Area" v-model="inventory.area">
                                <span class="text-danger" v-if="errors.area">{{ errors.area[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Remarks</label> 
                                <textarea name="" id="" cols="20" rows="3" class="form-control" placeholder="Remarks" v-model="inventory.remarks"></textarea>
                                <span class="text-danger" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveInventory">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="borrow-info-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">View Borrower Information</h2>
                </div>
                <div class="modal-body">
                    <div class="row" v-if="selectedItem">
                        <div class="col-md-12">
                            <h4>Employee Name : {{selectedItem.is_borrowed.employee_info.first_name + ' ' + selectedItem.is_borrowed.employee_info.last_name }}</h4>  
                        </div>
                        <div class="col-md-12">
                            <h4>Business Unit : {{selectedItem.is_borrowed.employee_info.cluster}}</h4>  
                        </div>
                        <div class="col-md-12">
                            <h4>Cluster : {{selectedItem.is_borrowed.employee_info.new_cluster}}</h4>  
                        </div>
                        <div class="col-md-12">
                            <h4>Borrow Date : {{selectedItem.is_borrowed.borrow_date}}</h4>  
                        </div>
                        <div class="col-md-12">
                            <h4>Status : {{selectedItem.is_borrowed.status}}</h4>  
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
            inventory : {
                'id' : '',
                'rfid_code' : '',
                'type' : 'N/A',
                'old_inventory_number' : 'N/A',
                'new_it_tag_qr_code_bar_code' : 'N/A',
                'serial_number' : 'N/A',
                'model' : 'N/A',
                'processor' : 'N/A',
                'manufacturer' : 'N/A',
                'supplier' : 'N/A',
                'purchase_company' : 'N/A',
                'delivery_date' : '',
                'order_number' : 'N/A',
                'retired_date' : '',
                'estimated_retirement_date' : '',
                'warranty_period' : 'N/A',
                'asset_code' : 'N/A',
                'purchase_cost' : '0.00',
                'insurance_date' : '',
                'os_name_and_version' : 'N/A',
                'tab_name' : 'N/A',
                'area' : 'N/A',
                'remarks' : 'N/A'
            },
            action : '',
            inventories : [],
            errors : [],
            currentPage: 0,
            itemsPerPage: 10,
            
            //Types
            types: [],

            //Borrower Information
            selectedItem : '',
        }
    },
    created () {
        this.getInventories();
        this.getTypes();
    },
    methods : {
        viewBorrowItem(item){
            this.selectedItem = item;
            $('#borrow-info-modal').modal('show');
        },
        addInventory(){
            let v = this;
            v.errors = [];
            v.inventory.id = '';
            v.inventory.rfid_code = '';
            v.inventory.type = 'N/A';
            v.inventory.old_inventory_number = 'N/A';
            v.inventory.new_it_tag_qr_code_bar_code = 'N/A';
            v.inventory.serial_number = 'N/A';
            v.inventory.model = 'N/A';
            v.inventory.processor = 'N/A';
            v.inventory.manufacturer = 'N/A';
            v.inventory.supplier = 'N/A';
            v.inventory.purchase_company = 'N/A';
            v.inventory.delivery_date = '';
            v.inventory.order_number = 'N/A';
            v.inventory.retired_date = '';
            v.inventory.estimated_retirement_date = '';
            v.inventory.warranty_period = 'N/A';
            v.inventory.asset_code = 'N/A';
            v.inventory.purchase_cost = '0.00';
            v.inventory.insurance_date = '';
            v.inventory.os_name_and_version = 'N/A';
            v.inventory.tab_name = 'N/A';
            v.inventory.area = 'N/A';
            v.inventory.remarks = 'N/A';
            v.action = 'New';
            this.getTypes();
            $('#inventory-modal').modal('show');
        },
        editInventory(inventory){
            let v = this;
            v.errors = [];
            v.inventory.id = inventory.id;
            v.inventory.rfid_code = inventory.rfid_code;
            v.inventory.type = inventory.type;
            v.inventory.old_inventory_number = inventory.old_inventory_number;
            v.inventory.new_it_tag_qr_code_bar_code = inventory.new_it_tag_qr_code_bar_code;
            v.inventory.serial_number = inventory.serial_number;
            v.inventory.model = inventory.model;
            v.inventory.processor = inventory.processor;
            v.inventory.manufacturer = inventory.manufacturer;
            v.inventory.supplier = inventory.supplier;
            v.inventory.purchase_company = inventory.purchase_company;
            v.inventory.delivery_date = inventory.delivery_date;
            v.inventory.order_number = inventory.order_number;
            v.inventory.retired_date = inventory.retired_date;
            v.inventory.estimated_retirement_date = inventory.estimated_retirement_date;
            v.inventory.warranty_period = inventory.warranty_period;
            v.inventory.asset_code = inventory.asset_code;
            v.inventory.purchase_cost = inventory.purchase_cost;
            v.inventory.insurance_date = inventory.insurance_date;
            v.inventory.os_name_and_version = inventory.os_name_and_version;
            v.inventory.tab_name = inventory.tab_name;
            v.inventory.area = inventory.area;
            v.inventory.remarks = inventory.remarks;
            v.action = 'Update';
            this.getTypes();
            $('#inventory-modal').modal('show');
        },
        saveInventory(){
            let v = this;
            Swal.fire({
                title: 'Are you sure you want to save this inventory?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    var postURL = `/inventories-store`;
                    if(v.action == "Update"){
                        formData.append('id', v.inventory.id ? v.inventory.id : "");
                        postURL = `/inventories-update`;
                    }
                    formData.append('rfid_code', v.inventory.rfid_code ? v.inventory.rfid_code : "");
                    formData.append('type', v.inventory.type ? v.inventory.type : "");
                    formData.append('old_inventory_number', v.inventory.old_inventory_number ? v.inventory.old_inventory_number : "");
                    formData.append('new_it_tag_qr_code_bar_code', v.inventory.new_it_tag_qr_code_bar_code ? v.inventory.new_it_tag_qr_code_bar_code : "");
                    formData.append('serial_number', v.inventory.serial_number ? v.inventory.serial_number : "");
                    formData.append('model', v.inventory.model ? v.inventory.model : "");
                    formData.append('processor', v.inventory.processor ? v.inventory.processor : "");
                    formData.append('manufacturer', v.inventory.manufacturer ? v.inventory.manufacturer : "");
                    formData.append('supplier', v.inventory.supplier ? v.inventory.supplier : "");
                    formData.append('purchase_company', v.inventory.purchase_company ? v.inventory.purchase_company : "");
                    formData.append('delivery_date', v.inventory.delivery_date ? v.inventory.delivery_date : "");
                    formData.append('order_number', v.inventory.order_number ? v.inventory.order_number : "");
                    formData.append('retired_date', v.inventory.retired_date ? v.inventory.retired_date : "");
                    formData.append('estimated_retirement_date', v.inventory.estimated_retirement_date ? v.inventory.estimated_retirement_date : "");
                    formData.append('warranty_period', v.inventory.warranty_period ? v.inventory.warranty_period : "");
                    formData.append('asset_code', v.inventory.asset_code ? v.inventory.asset_code : "");
                    formData.append('purchase_cost', v.inventory.purchase_cost ? v.inventory.purchase_cost : "");
                    formData.append('insurance_date', v.inventory.insurance_date ? v.inventory.insurance_date : "");
                    formData.append('os_name_and_version', v.inventory.os_name_and_version ? v.inventory.os_name_and_version : "");
                    formData.append('tab_name', v.inventory.tab_name ? v.inventory.tab_name : "");
                    formData.append('area', v.inventory.area ? v.inventory.area : "");
                    formData.append('remarks', v.inventory.remarks ? v.inventory.remarks : "");
                    axios.post(postURL, formData)
                    .then(response =>{
                        if(response.data.status == "success"){
                            Swal.fire('Inventory has been saved!', '', 'success');        
                            $('#inventory-modal').modal('hide'); 
                            this.getInventories();
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
        getInventories() {
            let v = this;
            v.inventories = [];
            axios.get('/inventories-data')
            .then(response => { 
                v.inventories = response.data;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getTypes() {
            let v = this;
            v.types = [];
            axios.get('/setting-types-data')
            .then(response => { 
                v.types = response.data;
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
        filteredInventories(){
            let self = this;
            if(self.inventories){
                return Object.values(self.inventories).filter(item => {
                    return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase())
                });
            }else{
                return [];
            }
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredInventories).length / this.itemsPerPage)
        },
        filteredInventoryQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredInventories.slice(index, index + this.itemsPerPage);
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
    @media (min-width: 1400px){
        .inventories-container{
            max-width: 1840px!important;
        }
    }
</style>