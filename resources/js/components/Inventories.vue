<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Inventories</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Configuration Settings</a>
                        </div>
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
                            <h3 class="card-label">Masterlist
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-primary mr-2 mt-1" @click="addInventory">New</button>
                            <button class="btn btn-info mr-2 mt-1" @click="uploadInventory">Upload Excel</button>
                            <button v-if="currentUser.user_id=='2693'" class="btn btn-warning mr-2 mt-1" @click="uploadCompanyInventory">Upload Company</button>
                             <download-excel
                                :data   = "filteredInventories"
                                :fields = "exportInventories"
                                class   = "btn btn-success mr-2 mt-1"
                                name    = "Inventories.xls">
                                    Download ({{ filteredInventories.length }})
                            </download-excel>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mt-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="cursor:pointer" @click="showFilter">
                                            <i class="fas fa-filter text-dark-50"></i>&nbsp;Advance Filter
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Search ID | Serial Number | Model | Type | RFID | OLD INVENTORY NUMBER"  v-model="keywords">
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <select class="form-control form-control-primary" v-model="filter_status">
                                        <option value="">Choose Filter</option>
                                        <option value="Assigned">Assigned</option>
                                        <option value="Available">Available</option>
                                        <option value="Borrowed">Borrowed</option>
                                        <option value="For Transfer">For Transfer</option>
                                        <option value="With RFID">With RFID</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            Show
                            <select v-model="itemsPerPage" @change="showPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            Total : <strong>{{ filteredInventories.length }}</strong> | With RFID : <strong>{{inventoriesWithRFID.length}}</strong>
                        </div>
                        <div class="float-right" v-if="check_selected_items.length > 0">
                            <a href="#" class="btn btn-sm btn-light-warning font-weight-bolder py-2 px-5 mt-2" @click="forDisposal">For Disposal ({{check_selected_items.length}})</a>
                            <a href="#" class="btn btn-sm btn-light-danger font-weight-bolder py-2 px-5 mt-2" @click="forMaintenance(check_selected_items)">For Maintenance ({{check_selected_items.length}})</a>
                            <a href="#" class="btn btn-sm btn-light-success font-weight-bolder py-2 px-5 mt-2" @click="clearSelection">Clear Selection ({{check_selected_items.length}})</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="10px">Select</th>
                                        <th class="text-center" width="50px">RFID</th>
                                        <th class="text-center">ID</th>
                                        <th>TYPE</th>
                                        <th>SERIAL NUMBER</th>
                                        <th>MODEL</th>
                                        <th>STATUS</th>
                                        <th class="text-center">AVAILABILITY</th>
                                        <th class="text-center">COMPANY</th>
                                        <th class="text-center">LOCATION</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredInventoryQueues" :key="i" >
                                        <td>
                                            <label class="container" v-if="item.status == 'For Disposal' || item.status == 'Disposed' || item.status=='For Maintenance'">
                                                <input type="checkbox" disabled>
                                                <span class="checkmark"></span>
                                            </label>
                                            <label class="container" v-else>
                                                <input type="checkbox" :id="item.id" :value="item.id" v-model="check_selected_items">
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td align="center">
                                            <small>{{item.epc}}</small>
                                        </td>
                                        <td align="center"><small>{{item.id}}</small></td>
                                        <td><small>{{item.type}}</small></td>
                                        <td><small>{{item.serial_number}}</small></td>
                                        <td><small>{{item.model}}</small></td>
                                        <td><small>{{item.status}}</small></td>
                                        <td align="center">
                                            <span v-if="item.is_borrowed" :class="getColorIsBorrow(item)" style="cursor:pointer" :title="item.is_borrowed.status" @click="viewBorrowItem(item)">{{ item.is_borrowed.is_assigned == 'true' ? "Assigned" : item.is_borrowed.status }}</span>
                                            <span v-else-if="item.is_transfer" class="label label-primary label-pill label-inline mr-2" style="cursor:pointer">For Transfer</span>
                                            <span v-else-if="item.status == 'Active'" class="label label-success label-pill label-inline mr-2" title="Available to Borrow" style="cursor:pointer" @click="assignItem(item)">Available</span>
                                            <span v-else class="label label-default label-pill label-inline mr-2" title="Available to Borrow" style="cursor:pointer">{{item.status}}</span>
                                        </td>
                                        <td align="center"><small>{{item.company}}</small></td>
                                        <td align="center"><small>{{item.location}}</small></td>
                                        <td align="center">
                                            <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editInventory(item)" title="Edit">
                                                <i class="flaticon-edit"></i>
                                            </button>
                                            <button @click="showQR('generate-qr-code?id='+item.id)" type="button" class="btn btn-light-primary btn-icon btn-sm" title="Print QR">
                                                <i class="flaticon-squares-4"></i>
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
                        </div>
                    </div>
                </div>  
            </div>  
        </div> 
    </div>

    <!-- Add Edit Inventory -->
    <div class="modal fade" id="inventory-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2"  @click="closeInventoryModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">{{ action }}</h2>
                </div>
                <div class="modal-body">
                    <h5 class="mt-5 mb-3 text-warning">RFID Registration 
                        <span v-if="activateImpinjDevice" class="label label-warning label-pill label-inline mr-2" style="cursor:pointer" title="Clear RFID" @click="clearRFID">Clear</span>
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="cursor:pointer" @click="getImpinjRFID">
                                        <i class="fas fa-credit-card text-dark-50"></i>&nbsp;&nbsp;Get RFID
                                    </span>
                                </div>
                                <input type="text" class="form-control" placeholder="EPC (Impinj RFID)"  v-model="inventory.epc" readonly><br>
                            </div>
                            <div v-if="rfidImpinjDetails">
                                <small v-if="rfidImpinjDetails.status =='success'">Scanner : {{ rfidImpinjDetails.reader_name}} | Scan Date : {{ rfidImpinjDetails.last_scan_date}}</small>   
                                <small v-else class="text-danger">{{rfidImpinjDetails.message}}</small>   
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="TID (Impinj RFID)"  v-model="inventory.tid" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mt-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="cursor:pointer" @click="getGeovisionRFID">
                                        <i class="fas fa-credit-card text-dark-50"></i>&nbsp;&nbsp;Get RFID
                                    </span>
                                </div>
                                <input type="text" class="form-control" placeholder="EPC (Geovision RFID)"  v-model="inventory.rfid_64" readonly><br>
                            </div>
                            <div v-if="rfidGeovisionDetails">
                                <small v-if="rfidGeovisionDetails.status =='success'">Scanner : {{ rfidGeovisionDetails.reader_name}} | Scan Date : {{ rfidGeovisionDetails.last_scan_date}}</small>   
                                <small v-else class="text-danger">{{rfidGeovisionDetails.message}}</small>   
                            </div>

                        </div>
                    </div>
                    <h5 class="mt-5 text-success">Inventory Information</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Type</label> 
                                <select class="form-control" v-model="inventory.type">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(type,b) in types" v-bind:key="b" :value="type.name"> {{ type.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.type">{{ errors.type[0] }}</span>
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
                                <label for="role">ID</label> 
                                <input type="text" class="form-control" placeholder="ID" v-model="inventory.id" disabled>
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
                                <input type="text" class="form-control" placeholder="Delivery Date" v-model="inventory.delivery_date">
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
                                <input type="text" class="form-control" placeholder="Retired Date" v-model="inventory.retired_date">
                                <span class="text-danger" v-if="errors.retired_date">{{ errors.retired_date[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="role">Estimated Retirement Date</label> 
                                <input type="text" class="form-control" placeholder="Estimated Retirement Date" v-model="inventory.estimated_retirement_date">
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
                                <input type="text" class="form-control" placeholder="Insurance Date" v-model="inventory.insurance_date">
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
                    <h5 class="text-info">Others</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Area</label> 
                                <input type="text" class="form-control" placeholder="Area" v-model="inventory.area">
                                <span class="text-danger" v-if="errors.area">{{ errors.area[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Company</label> 
                                <select class="form-control" v-model="inventory.company">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(company,b) in companies" v-bind:key="b" :value="company.company"> {{ company.company }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.company">{{ errors.company[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Location</label> 
                                <select class="form-control" v-model="inventory.location">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(location,b) in locations" v-bind:key="b" :value="location.location"> {{ location.location }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.location">{{ errors.location[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Building</label> 
                                <select class="form-control" v-model="inventory.building">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(building,b) in buildings" v-bind:key="b" :value="building.name"> {{ building.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.building">{{ errors.building[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Category</label> 
                                <select class="form-control" v-model="inventory.category">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(category,b) in categories" v-bind:key="b" :value="category.name"> {{ category.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.category">{{ errors.category[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role">Status</label> 
                                <select class="form-control" v-model="inventory.status">
                                    <option value="Active">Active</option>
                                    <option value="Defective">Defective</option>
                                    <option value="Disposed">Disposed</option>
                                    <option value="For Disposal">For Disposal</option>
                                    <option value="For Maintenance">For Maintenance</option>
                                    <option value="Owned">Owned</option>
                                    <option value="Replacement">Replacement</option>
                                    <option value="Spare">Spare</option>
                                    <option value="Rented">Rented</option>
                                    <option value="Loan Item">Loan Item</option>
                                    <option value="For Repair">For Repair</option>
                                </select>
                                <span class="text-danger" v-if="errors.status">{{ errors.status[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="inventory.status == 'Disposed'">
                            <div class="form-group">
                                <label for="role">Disposal Date</label> 
                                <input type="date" class="form-control" v-model="inventory.disposal_date">
                                <span class="text-danger" v-if="errors.disposal_date">{{ errors.disposal_date[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Remarks</label> 
                                <textarea name="" id="" cols="20" rows="3" class="form-control" placeholder="Remarks" v-model="inventory.remarks"></textarea>
                                <span class="text-danger" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="text-primary">Assignee & PMS</h5>
                    <hr>
                    <div class="timeline timeline-5">
                        <div class="timeline-items">
                          
                            <div class="timeline-item" v-if="inventory.is_borrowed">
                                <div class="timeline-media bg-light-primary">
                                    <span class="svg-icon svg-icon-primary svg-icon-md">
                                        <i class="fa fa-user text-primary"></i>
                                    </span>
                                </div>
                                <div class="timeline-desc timeline-desc-light-primary">
                                    <span class="font-weight-bolder text-primary">{{inventory.is_borrowed.borrow_date}}</span>
                                    <p class="font-weight-normal text-dark-50 pb-2">Assigned/Borrowed By: {{inventory.is_borrowed.employee_info.first_name + ' ' + inventory.is_borrowed.employee_info.last_name}}</p>
                                </div>
                            </div>
                           
                            
                            <div class="timeline-item" v-for="(item, i) in inventory.for_maintenance_logs" :key="i" >
                                <div class="timeline-media bg-light-success">
                                    <span class="svg-icon svg-icon-success svg-icon-md">
                                        <i class="fa fa-cog text-success"></i>
                                    </span>
                                </div>
                                <div class="timeline-desc timeline-desc-light-success">
                                    <span class="font-weight-bolder text-success">{{item.maintenance_date}}</span>
                                    <p class="font-weight-normal text-dark-50 pt-1">PMS (Remarks : {{item.remarks}} | Status : {{item.status}} | Prepared By : {{item.prepared_by_info.name}})</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" :disabled="savingDisable" @click="saveInventory">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Borrow Info -->
    <div class="modal fade" id="borrow-info-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h4 class="col-12 modal-title text-center">View Borrower Information</h4>
                </div>
                <div class="modal-body" v-if="selectedItem">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Employee Name : {{selectedItem.is_borrowed.employee_info.first_name + ' ' + selectedItem.is_borrowed.employee_info.last_name }}</h5>  
                        </div>
                        <div class="col-md-12">
                            <h5>Business Unit : {{selectedItem.is_borrowed.employee_info.cluster}}</h5>  
                        </div>
                        <div class="col-md-12">
                            <h5>Cluster : {{selectedItem.is_borrowed.employee_info.new_cluster}}</h5>  
                        </div>
                        <div class="col-md-12">
                            <h5>Borrow/Assign Date : {{selectedItem.is_borrowed.borrow_date}}</h5>  
                        </div>
                        <div class="col-md-12">
                            <h5>Ticket No. : {{selectedItem.is_borrowed.ticket_number}}</h5>  
                        </div>
                        <div class="col-md-12">
                            <h5>Status : {{selectedItem.is_borrowed.status}}</h5>  
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="selectedItem.is_borrowed.is_assigned" @change="updateUserInventoryIsAssigned(selectedItem.is_borrowed)" id="flexCheckDefault"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Is Assigned?
                                    </label>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
                <div class="modal-footer" v-if="selectedItem && selectedItem.is_borrowed">
                    <a v-if="selectedItem.type== 'Laptop' || selectedItem.type== 'Desktop' || selectedItem.type== 'Headset' || selectedItem.type== 'Portable Printer'" :href="'/generate-letter-of-undertaking?id=' + selectedItem.is_borrowed.id" target="_blank" class="btn btn-sm btn-primary">Letter of Undertaking</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Inventories -->
    <div class="modal fade" id="upload-inventories-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Upload Inventories</h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Upload Template (Excel File)*</label> 
                                <input type="file" id="upload_inventory_file" class="form-control" ref="file" accept=".xlsx" v-on:change="inventoryFileUpload()"/>
                                <span class="text-danger" v-if="errors.upload_inventory_file">{{ errors.upload_inventory_file[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" :disabled="uploadDisable" @click="saveUploadInventory">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Company Inventories -->
    <div class="modal fade" id="upload-company-inventories-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Upload Company Inventories</h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Upload Template (Excel File)*</label> 
                                <input type="file" id="upload_company_inventory_file" class="form-control" ref="file" accept=".xlsx" v-on:change="inventoryCompanyFileUpload()"/>
                                <span class="text-danger" v-if="errors.upload_inventory_file">{{ errors.upload_inventory_file[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" :disabled="uploadDisable" @click="saveUploadCompanyInventory">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="modal fade" id="apply-filter-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Apply Filter</h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Type</label> 
                                <select class="form-control" v-model="filter.type">
                                    <option value="">Choose Type</option>
                                    <option value="N/A">N/A</option>
                                    <option v-for="(type,b) in types" v-bind:key="b" :value="type.name"> {{ type.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Location</label> 
                                <select class="form-control" v-model="filter.location">
                                    <option value="">Choose Location</option>
                                    <option value="N/A">N/A</option>
                                    <option v-for="(location,b) in locations" v-bind:key="b" :value="location.location"> {{ location.location }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Category</label> 
                                <select class="form-control" v-model="filter.category">
                                    <option value="">Choose Category</option>
                                    <option value="N/A">N/A</option>
                                    <option v-for="(category,b) in categories" v-bind:key="b" :value="category.name"> {{ category.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Status</label> 
                                <select class="form-control" v-model="filter.status">
                                    <option value="">Choose Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Defective">Defective</option>
                                    <option value="Disposed">Disposed</option>
                                    <option value="For Disposal">For Disposal</option>
                                    <option value="For Maintenance">For Maintenance</option>
                                    <option value="Owned">Owned</option>
                                    <option value="Replacement">Replacement</option>
                                    <option value="Spare">Spare</option>
                                    <option value="No Status">No Status</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="getInventories">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- For Disposal -->
    <div class="modal fade" id="for-disposal-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">For Disposal Items</h2>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-checkable" id="kt_datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">TYPE</th>
                                    <th class="text-center">SERIAL NUMBER</th>
                                    <th class="text-center">MODEL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in check_selected_items" :key="i" >
                                    <td align="center">
                                        {{getType(item)}}
                                    </td>
                                    <td align="center">
                                        {{getSerialNumber(item)}}
                                    </td>
                                    <td align="center">
                                        {{getModel(item)}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveForDisposalItems" :disabled="forDisposalDisable">Request For Disposal</button>
                </div>
            </div>
        </div>
    </div>

     <!-- For Maintenance -->
    <div class="modal fade" id="for-maintenance-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">For Maintenance</h2>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-checkable" id="kt_datatable">
                            <thead>
                                <tr>
                                    <th class="text-center">TYPE</th>
                                    <th class="text-center">SERIAL NUMBER</th>
                                    <th class="text-center">MODEL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in for_maintenance_items" :key="i" >
                                    <td align="center">
                                        {{getType(item)}}
                                    </td>
                                    <td align="center">
                                        {{getSerialNumber(item)}}
                                    </td>
                                    <td align="center">
                                        {{getModel(item)}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveForMaintenance" :disabled="forMaintenanceDisable">For Maintenance</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Item -->
    <div class="modal fade" id="assign-item-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Assign Item</h2>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Select Employee</label> 
                                <select class="form-control" v-model="assign.employee_id">
                                    <option value="">Choose Employee</option>
                                    <option v-for="(employee,b) in employees" v-bind:key="b" :value="employee.id"> {{ employee.first_name + ' ' + employee.last_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Borrow/Assign Date</label> 
                                <input type="date" v-model="assign.borrow_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="assign.is_assigned" id="flexCheckDefault"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Is Assigned?
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-md btn-primary" @click="assignEmployeeItem" :disabled="assignItemDisable">Assign</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="view-qr-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">QR Code</h2>
                </div>
                <div class="modal-body" v-if="qr_link">
                    <pdf :src="qr_link"></pdf>
                   <iframe id="id-frame" :src="qr_link + '#page=1&zoom=300&scrollbar=1&toolbar=1&navpanes=1'" frameborder="0" width="100%" height="300px"></iframe>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" @click="printQR">Print</button>
                </div>
            </div>
        </div>
    </div>


</div>
</template>

<script>
import print from 'print-js'
import JsonExcel from 'vue-json-excel'
export default {
    components: {
        'downloadExcel': JsonExcel
    },
    data() {
        return {
            keywords: '',
            inventory : {
                'id' : '',
                'epc' : '',
                'tid' : '',
                'rfid_64' : '',
                'type' : '',
                'old_inventory_number' : 'N/A',
                // 'new_it_tag_qr_code_bar_code' : 'N/A',
                'serial_number' : '',
                'model' : '',
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
                // 'tab_name' : 'N/A',
                'area' : 'N/A',
                'location' : 'N/A',
                'category' : 'N/A',
                'status' : '',
                'remarks' : 'N/A',
                'disposal_date' : 'N/A',
            },
            action : '',
            inventories : [],
            errors : [],
            currentPage: 0,
            itemsPerPage: 10,
            
            //Settings Options
            types: [],
            companies: [],
            locations: [],
            buildings: [],
            categories: [],

            //Borrower Information
            selectedItem : '',

            filter_status : '',

            //Upload Inventories
            upload_inventory_file : '',
            uploadDisable : false,


            exportInventories : {
                'ASSIGNEE' :  {
                    callback: (value) => {
                        if(value.is_borrowed){
                            if(value.is_borrowed){
                                if(value.is_borrowed.employee_info){
                                    return value.is_borrowed.employee_info.first_name + ' ' + value.is_borrowed.employee_info.last_name;
                                }
                            }
                        }
                    }
                },
                'EPC' : 'epc',
                'TID' : 'tid',
                'RFID 64' : 'rfid_64',
                'ID' : 'id',
                'TYPE' : 'type',
                'OLD INVENTORY NUMBER' : 'old_inventory_number',
                'NEW IT TAG QR CODE/BAR CODE' : 'new_it_tag_qr_code_bar_code',
                'SERIAL NUMBER' : 'serial_number',
                'MODEL' : 'model',
                'PROCESSOR' : 'processor',
                'MANUFACTURER' : 'manufacturer',
                'SUPPLIER' : 'supplier',
                'PURCHASE COMPANY' : 'purchase_company',
                'DELIVERY DATE' : 'delivery_date',
                'ORDER NUMBER' : 'order_number',
                'RETIRED DATE' : 'retired_date',
                'ESTIMATED RETIREMENT DATE' : 'estimated_retirement_date',
                'WARRANTY PERIOD' : 'warranty_period',
                'ASSET CODE' : 'asset_code',
                'PURCHASE COST' : 'purchase_cost',
                'INSURANCE DATE' : 'insurance_date',
                'OS NAME AND VERSION' : 'os_name_and_version',
                // 'TAB NAME' : 'tab_name',
                'AREA' : 'area',
                'COMPANY' : 'company',
                'LOCATION' : 'location',
                'BUILDING' : 'building',
                'CATEGORY' : 'category',
                'STATUS' : 'status',
                'REMARKS' : 'remarks',
                'DISPOSAL DATE' : 'disposal_date',
            },

            //Filter
            filter : {
                type: '',
                location: '',
                building: '',
                category: '',
                status: ''
            },

            //RFID
            rfidImpinjDetails : '',
            rfidGeovisionDetails : '',

            //RFID Scanner Timer
            rfid_timer : '',

            activateImpinjDevice : '',
            activateGeovisionDevice : '',

            //Inventory
            savingDisable : false,

            //Selected Inventory Check
            check_selected_items : [],

            forDisposalDisable : false,
            assignItemDisable : false,
            assign : {
                inventory_id : '',
                employee_id : '',
                borrow_date : '',
            },

            for_maintenance_items : [],
            forMaintenanceDisable : false,

            currentUser : '',

            qr_link : '',
        }
    },
    created () {
        this.getCurrentUser();
        this.getInventories();
        this.getTypes();
        this.getCompanies();
        this.getLocations();
        this.getBuildings();
        this.getCategories();
        this.getActivatedImpinjDevice();
        this.getEmployees();
    },
    methods : {
        printQR(){
            printJS({printable: this.qr_link , type:'pdf', showModal:true});
        },
        showQR(link){
            this.qr_link = link;
            $('#view-qr-modal').modal('show');
        },
        showPage(){
            this.currentPage = 0;
        },
        getColorIsBorrow(item){
            if(item.is_borrowed.is_assigned == 'true'){
                return 'label label-primary label-pill label-inline mr-2';
            }else{
                return 'label label-warning label-pill label-inline mr-2';
            }
        },
        updateUserInventoryIsAssigned(){
            let formData = new FormData();
            formData.append('id', this.selectedItem.is_borrowed.id ? this.selectedItem.is_borrowed.id : "");
            formData.append('is_assigned', this.selectedItem.is_borrowed.is_assigned ? this.selectedItem.is_borrowed.is_assigned : "");
            axios.post(`/update-user-inventory-is-assigned`, formData)
            .then(response =>{
                if(response.data.status == 'saved'){
                    var index = this.inventories.findIndex(item => item.id == this.selectedItem.id);
                    this.inventories.splice(index,1,response.data.inventory);
                }
            })
        },
        assignEmployeeItem(){   
            let v = this;
            v.assignItemDisable = true;
            Swal.fire({
            title: 'Are you sure you want to assign this Inventory?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('inventory_id', v.assign.inventory_id ? v.assign.inventory_id : "");
                    formData.append('employee_id', v.assign.employee_id ? v.assign.employee_id : "");
                    formData.append('is_assigned', v.assign.is_assigned ? v.assign.is_assigned : "");
                    formData.append('borrow_date', v.assign.borrow_date ? v.assign.borrow_date : "");
                    axios.post(`/assign-employee-item`, formData)
                    .then(response =>{
                        if(response.data.status=='saved'){
                            v.assignItemDisable = false;
                            Swal.fire('Item has been assigned!', '', 'success')
                            .then(okay => {
                                if (okay) {
                                    // v.getInventories();
                                    var index = this.inventories.findIndex(item => item.id == v.assign.inventory_id);
                                    this.inventories.splice(index,1,response.data.inventory);
                                    $('#assign-item-modal').modal('hide');
                                }
                            });
                        }else{
                            Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            v.assignItemDisable = false;
                        }
                    })
                    .catch(error => {
                        v.assignItemDisable = false;
                        v.errors = error.response.data.errors;
                    })
                }else if (result.isDenied) {
                    Swal.fire('Employee assigned inventory not saved', '', 'info');
                    v.assignItemDisable = false;
                }
            })  
        },
        assignItem(item){
            this.assign.inventory_id = item.id;
            this.assign.employee_id = "";
            this.assign.borrow_date = "";
            $('#assign-item-modal').modal('show');
        },
        getEmployees(){
            let v = this;
            v.employees = '';
            axios.get('/all-employees')
            .then(response => { 
                v.employees = response.data;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getType(item){
            var rfid = Object.values(this.inventories).filter(i => {
                if(i.id == item){
                    return i;
                }
            });
            if(rfid.length > 0){
                return rfid[0].type;
            }
        },
        getSerialNumber(item){
            var rfid = Object.values(this.inventories).filter(i => {
                if(i.id == item){
                    return i;
                }
            });
            if(rfid.length > 0){
                return rfid[0].serial_number;
            }
        },
        getModel(item){
            var rfid = Object.values(this.inventories).filter(i => {
                if(i.id == item){
                    return i;
                }
            });
            if(rfid.length > 0){
                return rfid[0].model;
            }
        },
        saveForDisposalItems(){
            let v = this;
            v.forDisposalDisable = true;
            Swal.fire({
            title: 'Are you sure you want to request for disposal?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('items', JSON.stringify(v.check_selected_items));
                    axios.post(`/for-disposal-store`, formData)
                    .then(response =>{
                        if(response.data.status=='saved'){
                            v.forDisposalDisable = false;
                            Swal.fire(response.data.success + ' Items for disposal has been saved!', '', 'success')
                                .then(okay => {
                                    if (okay) {
                                        window.location.href = "/for-disposal-items?id=" + response.data.for_disposal_id;
                                    }
                                    });
                        }else{
                            Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            v.forDisposalDisable = false;
                        }
                    })
                    .catch(error => {
                        v.forDisposalDisable = false;
                        v.errors = error.response.data.errors;
                    })
                }else if (result.isDenied) {
                    // Swal.fire('Changes are not saved', '', 'info');
                    v.forDisposalDisable = false;
                }
            })  


        },
        clearSelection(){
            this.check_selected_items = [];
        },
        forDisposal(){
            $('#for-disposal-modal').modal('show');
        },
        saveForMaintenance(){
            let v = this;
            v.forMaintenanceDisable = true;
            Swal.fire({
            title: 'Are you sure you want to save this for maintenance?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('items', JSON.stringify(v.for_maintenance_items));
                    axios.post(`/for-maintenance-store`, formData)
                    .then(response =>{
                        if(response.data.status=='saved'){
                            v.forMaintenanceDisable = false;
                            Swal.fire(response.data.success + ' Items for maintenance has been saved!', '', 'success')
                                .then(okay => {
                                    if (okay) {
                                        window.location.href = "/for-maintenance?id=" + response.data.for_disposal_id;
                                    }
                                    });
                        }else{
                            Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            v.forMaintenanceDisable = false;
                        }
                    })
                    .catch(error => {
                        v.forMaintenanceDisable = false;
                        v.errors = error.response.data.errors;
                    })
                }else if (result.isDenied) {
                    // Swal.fire('Changes are not saved', '', 'info');
                    v.forMaintenanceDisable = false;
                }
            })  


        },
        forMaintenance(items){
            this.for_maintenance_items = items;
            $('#for-maintenance-modal').modal('show');
        },
        clearRFID(){
            let v = this;
            if(v.activateImpinjDevice){
                let formData = new FormData();
                formData.append('reader_name', v.activateImpinjDevice );
                axios.post(`/clear-rfid-log-registration-device`, formData)
                .then(response =>{
                    v.inventory.epc = '';
                    v.inventory.tid = '';
                    v.inventory.rfid_64 = '';
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            }
            
        },
        getActivatedImpinjDevice(){
            let v = this;
            v.activateImpinjDevice = '';
            v.activateGeovisionDevice = '';
            axios.get('/rfid-registration-impinj-devices-activated-data')
            .then(response => { 
                v.activateImpinjDevice = response.data.activate_impinj_device;
                v.activateGeovisionDevice = response.data.activate_geovision_device;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        closeInventoryModal(){
            $('#inventory-modal').modal('hide');
            this.stopTimer();
        },
        stopTimer(){
            clearInterval(this.rfid_timer);
        },
        scanRFID(){
            // this.rfid_timer = setInterval(this.getImpinjRFID, 3000)
            // this.rfid_timer = setInterval(this.getGeovisionRFID, 3000)
        },
        getImpinjRFID(){
            let v = this;
            axios.get('/api/impinj-rfid-log-registration-details?activate_impinj_device='+v.activateImpinjDevice)
            .then(response => { 
                if(response.data){
                    v.rfidImpinjDetails = response.data;
                    v.inventory.epc = response.data.epc ? response.data.epc : "No EPC Found";
                    v.inventory.tid = response.data.tid ? response.data.tid : "No EPC Found";
                }
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getGeovisionRFID(){
            let v = this;
            axios.get('/api/geovision-rfid-log-item-details?activate_geovision_device='+v.activateGeovisionDevice)
            .then(response => { 
                if(response.data){
                    v.rfidGeovisionDetails = response.data;
                    v.inventory.rfid_64 = response.data.rfid_64 ? response.data.rfid_64 : "No EPC Found";
                }
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        showFilter(){
            $('#apply-filter-modal').modal('show');
        },
        saveUploadInventory(){
            let v = this;
            v.uploadDisable = true;
            Swal.fire({
            title: 'Are you sure you want to upload this Inventory?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                        let formData = new FormData();
                        if(v.upload_inventory_file){
                            formData.append('upload_inventory_file', v.upload_inventory_file);
                        }
                        axios.post(`/save-upload-inventories`, formData)
                        .then(response =>{
                            if(response.data > 0){
                                $('#upload-inventories-modal').modal('hide');
                                v.getInventories();
                                document.getElementById("upload_inventory_file").value = '';
                                v.uploadDisable = false;
                                Swal.fire(response.data + ' inventories has been saved!', '', 'success');             
                                
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                                v.uploadDisable = false;
                            }
                        })
                        .catch(error => {
                            v.uploadDisable = false;
                            this.errors = error.response.data.errors;
                        })
                }else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info');
                    v.uploadDisable = false;
                }
            })    

        },
        saveUploadCompanyInventory(){
            let v = this;
            v.uploadDisable = true;
            Swal.fire({
            title: 'Are you sure you want to upload this Inventory?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                        let formData = new FormData();
                        if(v.upload_inventory_file){
                            formData.append('upload_inventory_file', v.upload_inventory_file);
                        }
                        axios.post(`/save-upload-company-inventories`, formData)
                        .then(response =>{
                            if(response.data > 0){
                                $('#upload-company-inventories-modal').modal('hide');
                                v.getInventories();
                                document.getElementById("upload_company_inventory_file").value = '';
                                v.uploadDisable = false;
                                Swal.fire(response.data + ' inventories has been saved!', '', 'success');             
                                
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                                v.uploadDisable = false;
                            }
                        })
                        .catch(error => {
                            v.uploadDisable = false;
                            this.errors = error.response.data.errors;
                        })
                }else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info');
                    v.uploadDisable = false;
                }
            })    

        },
        inventoryFileUpload(){
            var file = document.getElementById("upload_inventory_file");
            this.upload_inventory_file = file.files[0];
        },
        inventoryCompanyFileUpload(){
            var file = document.getElementById("upload_company_inventory_file");
            this.upload_inventory_file = file.files[0];
        },
        uploadInventory(){
            let v = this;
            v.uploadDisable = false;
            document.getElementById("upload_inventory_file").value = '';
            $('#upload-inventories-modal').modal('show');
        },
        uploadCompanyInventory(){
            let v = this;
            v.uploadDisable = false;
            document.getElementById("upload_inventory_file").value = '';
            $('#upload-company-inventories-modal').modal('show');
        },
        viewBorrowItem(item){
            this.selectedItem = item;
            $('#borrow-info-modal').modal('show');
        },
        addInventory(){
            let v = this;
            v.rfidImpinjDetails = '';
            v.errors = [];
            v.inventory.id = '';
            v.inventory.epc = '';
            v.inventory.tid = '';
            v.inventory.rfid_64 = '';
            v.inventory.type = '';
            v.inventory.old_inventory_number = 'N/A';
            v.inventory.new_it_tag_qr_code_bar_code = 'N/A';
            v.inventory.serial_number = '';
            v.inventory.model = '';
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
            v.inventory.company = 'N/A';
            v.inventory.location = 'N/A';
            v.inventory.building = 'N/A';
            v.inventory.category = 'N/A';
            v.inventory.status = '';
            v.inventory.remarks = 'N/A';
            v.inventory.disposal_date = '';
            v.action = 'New';
            this.getTypes();
            this.getCompanies();
            this.getLocations();
            this.getBuildings();
            this.getCategories();
            $('#inventory-modal').modal('show');
            this.scanRFID();
        },
        editInventory(inventory){
            let v = this;
            v.rfidImpinjDetails = '';
            v.errors = [];
            v.inventory = inventory;
            // v.inventory.epc = inventory.epc;
            // v.inventory.tid = inventory.tid;
            // v.inventory.rfid_64 = inventory.rfid_64;
            // v.inventory.type = inventory.type;
            // v.inventory.old_inventory_number = inventory.old_inventory_number;
            // v.inventory.new_it_tag_qr_code_bar_code = inventory.new_it_tag_qr_code_bar_code;
            // v.inventory.serial_number = inventory.serial_number;
            // v.inventory.model = inventory.model;
            // v.inventory.processor = inventory.processor;
            // v.inventory.manufacturer = inventory.manufacturer;
            // v.inventory.supplier = inventory.supplier;
            // v.inventory.purchase_company = inventory.purchase_company;
            // v.inventory.delivery_date = inventory.delivery_date;
            // v.inventory.order_number = inventory.order_number;
            // v.inventory.retired_date = inventory.retired_date;
            // v.inventory.estimated_retirement_date = inventory.estimated_retirement_date;
            // v.inventory.warranty_period = inventory.warranty_period;
            // v.inventory.asset_code = inventory.asset_code;
            // v.inventory.purchase_cost = inventory.purchase_cost;
            // v.inventory.insurance_date = inventory.insurance_date;
            // v.inventory.os_name_and_version = inventory.os_name_and_version;
            // v.inventory.tab_name = inventory.tab_name;
            // v.inventory.area = inventory.area;
            // v.inventory.company = inventory.company;
            // v.inventory.location = inventory.location;
            // v.inventory.building = inventory.building;
            // v.inventory.category = inventory.category;
            // v.inventory.status = inventory.status;
            // v.inventory.remarks = inventory.remarks;
            // v.inventory.disposal_date = inventory.disposal_date;
            v.action = 'Update';
            this.getTypes();
            this.getCompanies();
            this.getLocations();
            this.getBuildings();
            this.getCategories();
            $('#inventory-modal').modal('show');
        },
        saveInventory(){
            let v = this;
            this.savingDisable = true;
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
                    formData.append('epc', v.inventory.epc ? v.inventory.epc : "");
                    formData.append('tid', v.inventory.tid ? v.inventory.tid : "");
                    formData.append('rfid_64', v.inventory.rfid_64 ? v.inventory.rfid_64 : "");
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
                    // formData.append('tab_name', v.inventory.tab_name ? v.inventory.tab_name : "");
                    formData.append('area', v.inventory.area ? v.inventory.area : "");
                    formData.append('company', v.inventory.company ? v.inventory.company : "");
                    formData.append('location', v.inventory.location ? v.inventory.location : "");
                    formData.append('building', v.inventory.building ? v.inventory.building : "");
                    formData.append('category', v.inventory.category ? v.inventory.category : "");
                    formData.append('status', v.inventory.status ? v.inventory.status : "");
                    formData.append('remarks', v.inventory.remarks ? v.inventory.remarks : "");
                    formData.append('disposal_date', v.inventory.disposal_date ? v.inventory.disposal_date : "");
                    axios.post(postURL, formData)
                    .then(response =>{
                        
                        if(response.data.status == "success"){
                            Swal.fire('Inventory has been saved!', '', 'success');        
                            $('#inventory-modal').modal('hide'); 
                            // this.getInventories();
                            if(v.action == "Update"){
                                var index = this.inventories.findIndex(item => item.id == v.inventory.id);
                                this.inventories.splice(index,1,response.data.inventory);
                            }
                            
                            this.stopTimer();
                            this.savingDisable = false;
                        }else{
                            Swal.fire('Error: Cannot changed. Please try again.', '', 'error');   
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                        this.savingDisable = false;
                    })
                }else{
                    this.savingDisable = false;
                }   
            })
        },
        refresh(){
            window.location.href = '/inventories';
        },
        getInventories() {
            let v = this;
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            var status = urlParams.get('status');
            var location = urlParams.get('location');
            var type = urlParams.get('type');
            if(status){
                v.filter.status = status;
            }
            if(location){
                v.filter.location = location;
            }
            if(type){
                v.filter.type = type;
            }

            v.inventories = [];
            axios.get('/inventories-data?type='+v.filter.type+'&location='+v.filter.location+'&category='+v.filter.category+'&status='+v.filter.status)
            .then(response => { 
                v.inventories = response.data;
                $('#apply-filter-modal').modal('hide');
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
        getLocations() {
            let v = this;
            v.locations = [];
            axios.get('/setting-locations-data-options')
            .then(response => { 
                v.locations = response.data;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getCompanies() {
            let v = this;
            v.companies = [];
            axios.get('/setting-companies-data-options')
            .then(response => { 
                v.companies = response.data;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getBuildings() {
            let v = this;
            v.buildings = [];
            axios.get('/setting-buildings-data')
            .then(response => { 
                v.buildings = response.data;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getCategories() {
            let v = this;
            v.categories = [];
            axios.get('/setting-categories-data')
            .then(response => { 
                v.categories = response.data;
            })
            .catch(error => { 
                v.errors = error.response.data.error;
            })
        },
        getCurrentUser(){
            this.currentUser = '';
            axios.get('/current-user')
            .then(response => { 
                if(response.data){
                    this.currentUser = response.data;
                }
            })
            .catch(error => { 
                this.errors = error.response.data.error;
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
                self.resetStartRowUser();
                return Object.values(self.inventories).filter(item => {
                    if(self.filter_status == 'Available'){
                        if(item.is_borrowed == null && item.status == 'Active'){
                            return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.old_inventory_number == this.keywords 
                            || item.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.type.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.id == this.keywords || item.epc == this.keywords
                        }
                    }else if(self.filter_status == 'Assigned'){
                        if(item.is_borrowed){
                            if(item.is_borrowed.is_assigned == 'true'){
                                return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.old_inventory_number == this.keywords  
                                || item.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.type.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.id == this.keywords 
                                || item.epc == this.keywords
                            }
                        }
                    }else if(self.filter_status == 'Borrowed'){
                        if(item.is_borrowed){
                            if(item.is_borrowed.status == 'Borrowed' && !item.is_borrowed.is_assigned){
                                return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.old_inventory_number == this.keywords 
                                || item.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.type.toLowerCase().includes(this.keywords.toLowerCase()) 
                                || item.id == this.keywords 
                                || item.epc == this.keywords
                            }
                        }
                    }else if(self.filter_status == 'For Transfer'){
                        if(item.is_transfer){
                            return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.old_inventory_number == this.keywords 
                            || item.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.type.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.id == this.keywords 
                            || item.epc == this.keywords
                        }
                    }else if(self.filter_status == 'With RFID'){
                        if(item.epc){
                            return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.old_inventory_number == this.keywords 
                            || item.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.type.toLowerCase().includes(this.keywords.toLowerCase()) 
                            || item.id == this.keywords 
                            || item.epc == this.keywords
                        }
                    }else{
                        return item.serial_number.toLowerCase().includes(this.keywords.toLowerCase()) 
                        || item.old_inventory_number == this.keywords 
                        || item.model.toLowerCase().includes(this.keywords.toLowerCase()) 
                        || item.type.toLowerCase().includes(this.keywords.toLowerCase()) 
                        || item.id == this.keywords 
                        || item.epc == this.keywords
                    }
                });
            }else{
                return [];
            }
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredInventories).length / Number(this.itemsPerPage))
        },
        filteredInventoryQueues() {
            var index = this.currentPage * Number(this.itemsPerPage);
            var queues_array = this.filteredInventories.slice(index, index + Number(this.itemsPerPage));
            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }
            if(this.currentPage == -1) {
                this.currentPage = 0;
            }
            return queues_array;
        },
        inventoriesWithRFID(){
            let self = this;
            if(self.filteredInventories){
                self.resetStartRowUser();
                return Object.values(self.filteredInventories).filter(item => {
                    if(item.epc){
                        return item;
                    }
                });
            }else{
                return [];
            }
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