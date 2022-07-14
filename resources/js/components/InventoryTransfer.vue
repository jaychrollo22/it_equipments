<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Transfer</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Asset Transfer</a>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" @click="getInventoryTransfers" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
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
                        <div class="card-toolbar" v-if="currentUser">
                            <button v-if="currentUser.user.user_role.role == 'Administrator' || currentUser.user.user_role.role == 'IT Support'" class="btn btn-primary mr-2" @click="newTransfer">New</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Search here..." v-model="keywords">
                                </div>
                            </div>
                             <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <select class="form-control form-control-primary" v-model="filter_status">
                                        <option value="">Choose Filter</option>
                                        <option value="For Approval">For Approval</option>
                                        <option value="Pre-approved">Pre-approved</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Disapproved">Disapproved</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Transfer Code</th>
                                        <th class="text-center">Requested By</th>
                                        <th class="text-center">Date of Transfer</th>
                                        <th class="text-center">Date Requested</th>
                                        <th class="text-center">Location</th>
                                        <th class="text-center">Items</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(transfer, i) in filteredQueues" :key="i" >
                                       <td class="text-center"><small>{{transfer.transfer_code}}</small></td>
                                       <td class="text-center"><small>{{transfer.requested_by_info.name}}</small></td>
                                       <td class="text-center"><small>{{transfer.date_of_transfer}}</small></td>
                                       <td class="text-center"><small>{{transfer.date_requested}}</small></td>
                                       <td class="text-center"><small>{{transfer.transfer_location}}</small></td>
                                       <td class="text-center"><small>{{transfer.inventory_transfer_items.length}}</small></td>
                                       <td class="text-center"><a :href="'/transfer-approval?transfer_code='+transfer.transfer_code" target="_blank" :class="getColorStatus(transfer.status)">{{transfer.status}}</a></td>
                                       <td class="text-center">
                                            <div v-if="currentUser.user.user_role.role == 'Administrator' || currentUser.user.user_role.role == 'IT Support'">
                                                <button v-if="transfer.status == 'Approved' || transfer.status == 'Pre-approved'" type="button" class="btn btn-light-primary btn-icon btn-sm" disabled title="Not Available">
                                                    <i class="flaticon-edit"></i>
                                                </button>
                                                <button v-else type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editTransfer(transfer)" title="Update">
                                                    <i class="flaticon-edit"></i>
                                                </button>
                                                <a v-if="transfer.status == 'Approved'" :href="'print-inventory-transfer?transfer_code='+transfer.transfer_code" target="_blank" type="button" class="btn btn-light-primary btn-icon btn-sm" title="Print">
                                                    <i class="flaticon2-printer"></i>
                                                </a>
                                                <a v-else type="button" class="btn btn-light-default btn-icon btn-sm" title="Not Available" >
                                                    <i class="flaticon2-printer"></i>
                                                </a>
                                            </div>
                                       </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                                
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transfer-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
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
                                <label for="role">Department</label> 
                                <select class="form-control" v-model="transfer.transfer_department">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(department,b) in departmentOptions" v-bind:key="b" :value="department.name"> {{ department.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.transfer_department">{{ errors.transfer_department[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Date Requested</label> 
                                <input type="date" class="form-control" placeholder="Date Requested" v-model="transfer.date_requested">
                                <span class="text-danger" v-if="errors.date_requested">{{ errors.date_requested[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Local No.</label> 
                                <input type="text" class="form-control" placeholder="Local Number" v-model="transfer.local_number">
                                <span class="text-danger" v-if="errors.local_number">{{ errors.local_number[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Date of Transfer</label> 
                                <input type="date" class="form-control" placeholder="Date of Transfer" v-model="transfer.date_of_transfer">
                                <span class="text-danger" v-if="errors.date_of_transfer">{{ errors.date_of_transfer[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Transfer Company</label> 
                                <select class="form-control" v-model="transfer.transfer_company">
                                    <option value="">Choose</option>
                                    <option v-for="(company,b) in companyOptions" v-bind:key="b" :value="company.company"> {{ company.company }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.transfer_company">{{ errors.transfer_company[0] }}</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Transfer Location</label> 
                                <select class="form-control" v-model="transfer.transfer_location">
                                    <option value="">Choose</option>
                                    <option value="N/A">N/A</option>
                                    <option v-for="(location,b) in locationOptions" v-bind:key="b" :value="location.name"> {{ location.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.transfer_location">{{ errors.transfer_location[0] }}</span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Remarks</label> 
                                <textarea class="form-control" v-model="transfer.remarks" placeholder="Remarks"></textarea>
                                <span class="text-danger" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Effective Date</label> 
                                <input type="date" class="form-control" placeholder="Date of Transfer" v-model="transfer.effective_date">
                                <span class="text-danger" v-if="errors.effective_date">{{ errors.effective_date[0] }}</span>
                            </div>
                        </div>
                    </div>
                    <h5>Select Items to Transfer</h5>
                    <div class="row mb-2">
                        <div class="col-lg-12 mt-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                       <i class="fas fa-shopping-cart"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" v-model="itemName" placeholder="Search Item (Serial Number/Model)" @keyup.enter="searchTransferItem">
                                <div class="input-group-append">
                                    <a href="#" @click="searchTransferItem" class="btn font-weight-bold btn-success btn-icon">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="kt_datatable">
                            <thead>
                                <tr>
                                   <th class="text-center">ID</th>
                                   <th class="text-center">Type</th>
                                    <th class="text-center">Model</th>
                                    <th class="text-center">Serial No.</th>
                                    <th class="text-center">Current Company</th>
                                    <th class="text-center">Current Location</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in items" :key="i" >
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.id}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.company}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.location}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <button v-if="validateTransferItem(item)" class="btn btn-md btn-primary" @click="addTransferItem(item)">Select</button>
                                        <button v-else class="btn btn-md btn-primary" disabled>Selected</button>
                                    </td>
                                </tr>
                                <tr v-if="items.length == 0">
                                    <td colspan="6" align="center">No Items Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="selectedItems.length > 0">
                        <h5>Selected Items ({{selectedItems.length}})</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Model</th>
                                        <th class="text-center">Serial No.</th>
                                        <th class="text-center">Current Company</th>
                                        <th class="text-center">Current Location</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in selectedItems" :key="i" >
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.id}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.company}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.location}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <button class="btn btn-md btn-danger" @click="removeTransferItem(item)">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td><small>IT Head Approver</small> </td>
                                    <td>
                                        <select class="form-control" v-model="transfer.approved_by_it_head">
                                           <option value="">Choose IT Approver</option>
                                           <option v-for="(item, i) in systemApproversIT" :key="i"  :value="item.user_id" >{{item.user.name}}</option>
                                       </select>
                                       <span class="text-danger" v-if="errors.approved_by_it_head">{{ errors.approved_by_it_head[0] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><small>Finance Head Approver</small> </td>
                                    <td>
                                        <select class="form-control" v-model="transfer.approved_by_finance">
                                           <option value="">Choose Finance Approver</option>
                                           <option v-for="(item, i) in systemApproversFinance" :key="i"  :value="item.user_id" >{{item.user.name}}</option>
                                       </select>
                                       <span class="text-danger" v-if="errors.approved_by_finance">{{ errors.approved_by_finance[0] }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <button v-if="selectedItems.length > 0" class="btn btn-md btn-primary float-right" @click="saveTransfer" :disabled="transferDisable">{{transferDisableLabel}}</button>
                    <button v-else class="btn btn-md btn-primary float-right" disabled>Save Transfer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transfer-edit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
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
                        <!-- <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Requested Name</label> 
                                <multiselect
                                        v-model="transfer.requested_by"
                                        :options="userOptions"
                                        :multiple="false"
                                        track-by="id"
                                        :custom-label="customLabelUser"
                                        placeholder="Select User"
                                >
                                </multiselect>
                                <span class="text-danger" v-if="errors.requested_by">{{ errors.requested_by[0] }}</span>
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Department</label> 
                                <select class="form-control" v-model="transfer.transfer_department">
                                    <option value="N/A">N/A</option>
                                    <option v-for="(department,b) in departmentOptions" v-bind:key="b" :value="department.name"> {{ department.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.transfer_department">{{ errors.transfer_department[0] }}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Date Requested</label> 
                                <input type="date" class="form-control" placeholder="Date Requested" v-model="transfer.date_requested">
                                <span class="text-danger" v-if="errors.date_requested">{{ errors.date_requested[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Local No.</label> 
                                <input type="text" class="form-control" placeholder="Local Number" v-model="transfer.local_number">
                                <span class="text-danger" v-if="errors.local_number">{{ errors.local_number[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Date of Transfer</label> 
                                <input type="date" class="form-control" placeholder="Date of Transfer" v-model="transfer.date_of_transfer">
                                <span class="text-danger" v-if="errors.date_of_transfer">{{ errors.date_of_transfer[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Transfer Company</label> 
                                <select class="form-control" v-model="transfer.transfer_company">
                                    <option value="">Choose</option>
                                    <option v-for="(company,b) in companyOptions" v-bind:key="b" :value="company.company"> {{ company.company }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.transfer_company">{{ errors.transfer_company[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Transfer Location</label> 
                                <select class="form-control" v-model="transfer.transfer_location">
                                    <option value="">Choose</option>
                                    <option value="N/A">N/A</option>
                                    <option v-for="(location,b) in locationOptions" v-bind:key="b" :value="location.name"> {{ location.name }}</option>
                                </select>
                                <span class="text-danger" v-if="errors.transfer_location">{{ errors.transfer_location[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Remarks</label> 
                                <textarea class="form-control" v-model="transfer.remarks" placeholder="Remarks"></textarea>
                                <span class="text-danger" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Effective Date</label> 
                                <input type="date" class="form-control" placeholder="Date of Transfer" v-model="transfer.effective_date">
                                <span class="text-danger" v-if="errors.effective_date">{{ errors.effective_date[0] }}</span>
                            </div>
                        </div>
                    </div>
                    <h5>Select Items to Transfer</h5>
                    <div class="row mb-2">
                        <div class="col-lg-12 mt-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                       <i class="fas fa-shopping-cart"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" v-model="itemName" placeholder="Search Item (Serial Number/Model)" @keyup.enter="searchTransferItem">
                                <div class="input-group-append">
                                    <a href="#" @click="searchTransferItem" class="btn font-weight-bold btn-success btn-icon">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="kt_datatable">
                            <thead>
                                <tr>
                                   <th class="text-center">ID</th>
                                   <th class="text-center">Type</th>
                                    <th class="text-center">Model</th>
                                    <th class="text-center">Serial No.</th>
                                    <th class="text-center">Company</th>
                                    <th class="text-center">Location</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in items" :key="i" >
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.id}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.company}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;"><small>{{item.location}}</small></td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <button v-if="validateTransferItem(item)" class="btn btn-md btn-primary" @click="addTransferEditItem(item)">Select</button>
                                        <button v-else class="btn btn-md btn-primary" disabled>Selected</button>
                                    </td>
                                </tr>
                                <tr v-if="items.length == 0">
                                    <td colspan="7" align="center">No Items Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="selectedEditItems.length > 0">
                        <h5>Selected Items ({{selectedEditItems.length}})</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Model</th>
                                        <th class="text-center">Serial No.</th>
                                        <th class="text-center">Company</th>
                                        <th class="text-center">Location</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in selectedEditItems" :key="i" >
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.id}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.company}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.location}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <button v-if="item.status == 'Received'" class="btn btn-md btn-danger" disabled>{{item.status}}</button>
                                            <button v-else class="btn btn-md btn-danger"  @click="removeTransferRemoveItem(item)">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td><small>IT Head Approver</small> </td>
                                    <td>
                                        <select class="form-control" v-model="transfer.approved_by_it_head">
                                           <option value="">Choose IT Approver</option>
                                           <option v-for="(item, i) in systemApproversIT" :key="i"  :value="item.user_id" >{{item.user.name}}</option>
                                       </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><small>Finance Head Approver</small> </td>
                                    <td>
                                        <select class="form-control" v-model="transfer.approved_by_finance">
                                           <option value="">Choose Finance Approver</option>
                                           <option v-for="(item, i) in systemApproversFinance" :key="i"  :value="item.user_id" >{{item.user.name}}</option>
                                       </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <button v-if="selectedEditItems.length > 0 && (transfer.status == 'For Approval' || transfer.status == 'Disapproved')" class="btn btn-md btn-primary float-right" @click="saveEditTransfer" :disabled="transferDisable">{{transferDisableLabel}}</button>
                    <button v-else class="btn btn-md btn-primary float-right" disabled>Update Transfer</button>
                </div>
            </div>
        </div>
    </div>




</div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
    import Multiselect from 'vue-multiselect';
    export default {
        components: {
            Multiselect,
        },
        data() {
            return {
                filter_status : '',
                keywords: '',
                action : '',
                inventoryTransfers : [],
                transfer : {
                    requested_by : '',
                    transfer_department : '',
                    transfer_company : '',
                    date_requested : '',
                    local_number : '',
                    date_of_transfer : '',
                    remarks : '',
                    effective_date : '',
                    company : '',
                    location : '',
                },
                errors : [],
                selectedItems : [],
                selectedEditItems : [],
                selectedRemovedItems : [],
                
                items : [],
                itemName : '',
                
                //Options
                userOptions : [],
                departmentOptions : [],
                companyOptions : [],
                locationOptions : [],

                systemApproversIT : [],
                systemApproversFinance : [],

                //User
                currentPage: 0,
                itemsPerPage: 10, 

                currentUser : '',

                transferDisable : false,
                transferDisableLabel : 'Save',
            }
        },
        created () {
            this.getCurrentUser();
            this.getInventoryTransfers();
            this.getUsers();
            this.getDepartments();
            this.getCompanies();
            this.getLocations();
            this.getSystemApproversIT();
            this.getSystemApproversFinance();
            
        },
        methods: {
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
            getColorStatus(item){
                if(item == 'For Approval'){
                    return 'label label-warning label-pill label-inline mr-2';
                }else if(item == 'Pre-approved'){
                    return 'label label-info label-pill label-inline mr-2';
                }else if(item == 'Approved'){
                    return 'label label-primary label-pill label-inline mr-2';
                }else if(item == 'Disapproved'){
                    return 'label label-danger label-pill label-inline mr-2';
                }else{
                    return 'label label-default label-pill label-inline mr-2';
                }
            },
            getSystemApproversIT(){
                this.systemApproversIT = [];
                 axios.get('/system-approver-it-data')
                .then(response => { 
                    if(response.data){
                        this.systemApproversIT = response.data;
                    }
                   
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                })
            },
            getSystemApproversFinance(){
                this.systemApproversFinance = [];
                 axios.get('/system-approver-finance-data')
                .then(response => { 
                    if(response.data){
                        this.systemApproversFinance = response.data;
                    }
                   
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                })
            },
            saveEditTransfer(){
                let v = this;
                v.transferDisable = true;
                  Swal.fire({
                    title: 'Are you sure you want to update this transfer?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    }).then((result) => {
                    if (result.isConfirmed) {
                        v.transferDisableLabel = 'Saving...Please wait...';
                        let formData = new FormData();
                        formData.append('id', v.transfer.id ? v.transfer.id : "");
                        // formData.append('requested_by', v.transfer.requested_by ? v.transfer.requested_by.id : "");
                        formData.append('transfer_department', v.transfer.transfer_department ? v.transfer.transfer_department : "");
                        formData.append('transfer_company', v.transfer.transfer_company ? v.transfer.transfer_company : "");
                        formData.append('date_requested', v.transfer.date_requested ? v.transfer.date_requested : "");
                        formData.append('local_number', v.transfer.local_number ? v.transfer.local_number : "");
                        formData.append('date_of_transfer', v.transfer.date_of_transfer ? v.transfer.date_of_transfer : "");
                        formData.append('transfer_location', v.transfer.transfer_location ? v.transfer.transfer_location : "");
                        formData.append('transfer_inventories', v.selectedEditItems ? JSON.stringify(v.selectedEditItems) : "");
                        formData.append('remarks', v.transfer.remarks ? v.transfer.remarks : "");
                        formData.append('effective_date', v.transfer.effective_date ? v.transfer.effective_date : "");
                        formData.append('approved_by_it_head', v.transfer.approved_by_it_head ? v.transfer.approved_by_it_head : "");
                        formData.append('approved_by_finance', v.transfer.approved_by_finance ? v.transfer.approved_by_finance : "");
                        axios.post(`/update-inventory-transfer`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Transfer has been saved! Total (' + response.data.save_count+')', '', 'success');  
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Transfer has been saved! Total (' + response.data.save_count+')',
                                    icon: 'success',
                                    confirmButtonText: 'Okay',
                                    timer : 5000
                                }).then(okay => {
                                    if (okay) {
                                        v.transferDisable = false;
                                        v.transferDisableLabel = 'Save';
                                        location.reload();
                                    }
                                });      
                                
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');
                                v.transferDisable = false;
                                v.transferDisableLabel = 'Save';
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                            v.transferDisable = false;
                            v.transferDisableLabel = 'Save';
                        })
                    }else{
                        v.transferDisable = false;
                        v.transferDisableLabel = 'Save';
                    }
                });
            },
            removeTransferRemoveItem(item){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to remove this item? (' + item.type + ' : ' + item.model + ')',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        var index = this.selectedEditItems.findIndex(item_select => item_select.id == item.id);
                        this.selectedEditItems.splice(index, 1);

                        let formData = new FormData();
                        formData.append('inventory_id', item.id ? item.id : "");
                        formData.append('inventory_transfer_id', v.transfer.id? v.transfer.id : "");
                        axios.post(`/remove-inventory-transfer-item`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                this.selectedEditItems.splice(item, 1);
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        })
                    }
                })
                
                
               
            },
            addTransferEditItem(item){
                let v = this;
                v.selectedEditItems.push({
                    id : item.id,
                    model : item.model,
                    serial_number : item.serial_number,
                    type : item.type,
                    location : item.location,
                    company : item.company,
                    tag : 'new',
                    status : 'Pending'
                });
                this.items.splice(item, 1);
            },
            editTransfer(transfer){
                let v = this;
                v.action = 'Edit';
                v.transfer.id = transfer.id;
                // v.transfer.requested_by = transfer.requested_by_info;
                v.transfer.transfer_department = transfer.transfer_department;
                v.transfer.transfer_company = transfer.transfer_company;
                v.transfer.date_requested = transfer.date_requested;
                v.transfer.local_number = transfer.local_number;
                v.transfer.date_of_transfer = transfer.date_of_transfer;
                v.transfer.transfer_location = transfer.transfer_location;
                v.transfer.remarks = transfer.remarks;
                v.transfer.effective_date = transfer.effective_date;
                v.transfer.approved_by_it_head = transfer.approved_by_it_head;
                v.transfer.approved_by_finance = transfer.approved_by_finance;
                v.transfer.status = transfer.status;

                if(transfer.inventory_transfer_items){
                    v.selectedEditItems = [];
                    transfer.inventory_transfer_items.forEach(item => {
                        v.selectedEditItems.push({
                            id : item.inventory_info.id,
                            model : item.inventory_info.model,
                            serial_number : item.inventory_info.serial_number,
                            type : item.inventory_info.type,
                            location : item.inventory_info.location,
                            company : item.inventory_info.company,
                            tag : 'edit',
                            status : item.status
                        });
                    });
                }
                $('#transfer-edit-modal').modal('show');
            },
            //Save New Transfer
            saveTransfer(){
                let v = this;
                v.transferDisable = true;
                  Swal.fire({
                    title: 'Are you sure you want to save this transfer?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    }).then((result) => {
                    if (result.isConfirmed) {
                        v.transferDisableLabel = 'Saving...Please wait...';
                        let formData = new FormData();
                        // formData.append('requested_by', v.transfer.requested_by ? v.transfer.requested_by.id : "");
                        formData.append('transfer_department', v.transfer.transfer_department ? v.transfer.transfer_department : "");
                        formData.append('transfer_company', v.transfer.transfer_company ? v.transfer.transfer_company : "");
                        formData.append('date_requested', v.transfer.date_requested ? v.transfer.date_requested : "");
                        formData.append('local_number', v.transfer.local_number ? v.transfer.local_number : "");
                        formData.append('date_of_transfer', v.transfer.date_of_transfer ? v.transfer.date_of_transfer : "");
                        formData.append('transfer_location', v.transfer.transfer_location ? v.transfer.transfer_location : "");
                        formData.append('transfer_inventories', v.selectedItems ? JSON.stringify(v.selectedItems) : "");
                        formData.append('remarks', v.transfer.remarks ? v.transfer.remarks : "");
                        formData.append('effective_date', v.transfer.effective_date ? v.transfer.effective_date : "");
                        formData.append('approved_by_it_head', v.transfer.approved_by_it_head ? v.transfer.approved_by_it_head : "");
                        formData.append('approved_by_finance', v.transfer.approved_by_finance ? v.transfer.approved_by_finance : "");
                        axios.post(`/save-inventory-transfer`, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Transfer has been saved! Total (' + response.data.save_count+')', '', 'success');  
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Transfer has been saved! Total (' + response.data.save_count+')',
                                    icon: 'success',
                                    confirmButtonText: 'Okay',
                                    timer : 5000
                                }).then(okay => {
                                    if (okay) {
                                        location.reload();
                                    }
                                });      
                                
                            }else{
                                Swal.fire('Error: Cannot saved. Check details and try again.', '', 'error');
                                v.transferDisable = false;
                                v.transferDisableLabel = 'Save';
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error: Cannot saved. Check details and try again.', '', 'error');
                            this.errors = error.response.data.errors;
                            v.transferDisable = false;
                            v.transferDisableLabel = 'Save';
                        })
                    }else{
                        v.transferDisable = false;
                        v.transferDisableLabel = 'Save';
                    }
                });
            },
            removeTransferItem(item){
                this.selectedItems.splice(item, 1);
            },
            addTransferItem(item){
                let v = this;
                v.selectedItems.push({
                    id : item.id,
                    model : item.model,
                    serial_number : item.serial_number,
                    type : item.type,
                    location : item.location,
                    company : item.company,
                });
                this.items.splice(item, 1);
            },
            validateTransferItem(item){
                if(this.action == 'New'){
                    var hasMatch = this.selectedItems.filter(function (val) {
                        return !!(val.id === item.id);                               
                    });
                    if(hasMatch.length > 0){
                        return false;
                    }else{
                        return true;
                    }
                }else if(this.action == 'Edit'){
                    var hasMatch = this.selectedEditItems.filter(function (val) {
                        return !!(val.id === item.id);                               
                    });
                    if(hasMatch.length > 0){
                        return false;
                    }else{
                        return true;
                    }
                }
                
            },
            searchTransferItem(){
                let v = this;
                v.items=[];
                if(v.itemName){
                    axios.get('/item-search?item_name=' + v.itemName)
                    .then(response => { 
                        if(response.data){
                        v.items = response.data;
                        }else{
                            Swal.fire('No Item Found! Please try again', '', 'warning');
                        }
                    })
                    .catch(error => { 
                        v.errors = error.response.data.error;
                    })
                }else{
                    Swal.fire('Item is required', '', 'error');
                }
            },
            customLabelUser (user) {
                if(user){
                    return `${ user.name }`;
                }else{
                   return '';
                }
            },
            getUsers(){
                let v = this;
                axios.get('/get-users-data')
                .then(response => { 
                    v.userOptions = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                }) 
            },
            getDepartments(){
                let v = this;
                axios.get('/setting-departments-data')
                .then(response => { 
                    v.departmentOptions = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                }) 
            },
            getCompanies(){
                let v = this;
                v.companyOptions = [];
                axios.get('/setting-companies-data-options')
                .then(response => { 
                    v.companyOptions = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            getLocations(){
                let v = this;
                axios.get('/setting-locations-data')
                .then(response => { 
                    v.locationOptions = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                }) 
            },
            getInventoryTransfers(){
                let v = this;
                v.inventoryTransfers = [];
                axios.get('/inventory-transfer-data')
                .then(response => { 
                    v.inventoryTransfers = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.error;
                }) 
            },
            newTransfer() {
                let v = this;
                v.action = 'New';
                // v.transfer.requested_by = '';
                v.transfer.transfer_department = '';
                v.transfer.transfer_company = '';
                v.transfer.date_requested = '';
                v.transfer.local_number = '';
                v.transfer.date_of_transfer = '';
                v.transfer.remarks = '';
                v.transfer.effective_date = '';
                v.transfer.transfer_location = '';
                v.selectedItems = [];
                $('#transfer-modal').modal('show');
            },

            //Pagination
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
            },
        },
        computed: {
            filteredTransfers(){
                let self = this;
                if(self.inventoryTransfers){
                    return Object.values(self.inventoryTransfers).filter(transfer => {
                        if(self.filter_status){
                            if(self.filter_status == 'For Approval'){
                                if(transfer.status == 'For Approval'){
                                    return transfer.transfer_code.toLowerCase().includes(this.keywords.toLowerCase())
                                }
                            }
                            if(self.filter_status == 'Approved'){
                                if(transfer.status == 'Approved'){
                                    return transfer.transfer_code.toLowerCase().includes(this.keywords.toLowerCase())
                                }
                            }
                            if(self.filter_status == 'Pre-approved'){
                                if(transfer.status == 'Pre-approved'){
                                    return transfer.transfer_code.toLowerCase().includes(this.keywords.toLowerCase())
                                }
                            }
                            if(self.filter_status == 'Disapproved'){
                                if(transfer.status == 'Disapproved'){
                                    return transfer.transfer_code.toLowerCase().includes(this.keywords.toLowerCase())
                                }
                            }
                        }else{
                            return transfer.transfer_code.toLowerCase().includes(this.keywords.toLowerCase())
                        }
                        
                    });
                }else{
                    return [];
                }
               
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredTransfers).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredTransfers.slice(index, index + this.itemsPerPage);

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