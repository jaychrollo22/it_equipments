<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content ">
            <!--begin::Subheader-->
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap employees-container">
                    <!--begin::Info-->
                    <div class="d-flex align-items-center flex-wrap mr-1 mt-10">
                        <!--begin::Heading-->
                        <div class="d-flex flex-column">
                            <!--begin::Title-->
                            <h2 class="text-white font-weight-bold my-2 mr-5">Good Day!</h2>
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
                                <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Welcome to IT Equipment Portal</a>
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
                    <div class="row">
                        <div class="col-sm-6">
                            <!--begin::Engage Widget 9-->
                            <div class="card card-custom card-stretch gutter-b">
                                <div class="card-body d-flex p-0">
                                    <div class="flex-grow-1 p-20 pb-40 card-rounded flex-grow-1 bgi-no-repeat" style="background-color: #7889E8; background-position: calc(100% + 0.5rem) bottom; background-size: 50% auto; background-image: url(assets/media/svg/patterns/rhone.svg)">
                                        <h2 class="text-white pb-5 font-weight-bolder">Borrow Item</h2>
                                        <p class="text-white pb-5 font-size-h5">1. Search or Scan Employee ID
                                        <br>2. Search or Scan Items then tap "Select".
                                        <br>3. Review and Submit
                                        <br></p>
                                        <a href="#" class="btn btn-success font-weight-bold font-size-h3 px-12 py-5" @click="showBorrowScanItem">Click Here</a>
                                    </div>
                                </div>
                            </div>
                            <!--end::Engage Widget 9-->
                        </div>
                        <div class="col-sm-6">
                            <!--begin::Engage Widget 10-->
                            <div class="card card-custom card-stretch gutter-b">
                                <div class="card-body d-flex p-0">
                                    <div class="flex-grow-1 p-20 pb-40 bg-warning card-rounded flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: 50% auto; background-image: url(assets/media/svg/patterns/rhone.svg)">
                                        <h2 class="text-inverse-warning pb-5 font-weight-bolder">Return Item</h2>
                                        <p class="text-inverse-warning pb-5 font-size-h5">1. Search or Scan Employee ID
                                        <br>2. Select Items then tap "Return" 
                                        <br>3. Then click "Yes" to save.
                                        <br></p>
                                        <a href="#" class="btn btn-danger font-weight-bold font-size-h3 px-12 py-5" @click="showReturnScanItem">Click Here</a>
                                    </div>
                                </div>
                            </div>
                            <!--end::Engage Widget 10-->
                        </div>
                    </div>
                </div>
            </div>
    </div>


    <!-- Borrow Item -->
    <div class="modal fade" id="borrow-item-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Borrow Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
                        <!--begin::Wizard Nav-->
                        <div class="wizard-nav border-bottom">
                            <div class="wizard-steps p-8 p-lg-10">
                                <!--begin::Wizard Step 1 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="borrow_step_1">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-search"></i>
                                        <h3 class="wizard-title">1. Search Employee</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 1 Nav-->
                                <!--begin::Wizard Step 2 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="borrow_step_2">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-list"></i>
                                        <h3 class="wizard-title">2. Select Items</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 2 Nav-->
                                <!--begin::Wizard Step 5 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="borrow_step_3">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-globe"></i>
                                        <h3 class="wizard-title">3. Review and Submit</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow last">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 5 Nav-->
                            </div>
                        </div>
                    </div>
                    <!-- Step 1 -->
                    <div v-if="borrow_step_1 == 'current'" class="row" >
                        <div class="col-lg-12 mt-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-address-card"></i>
                                    </span>
                                </div>
                                <input id="borrow_id_number" type="text" class="form-control" v-mask="mask" v-model="employeeIDNumber" placeholder="ID Number (XX-XXXXX-XXXX)" @keyup.enter="searchEmployee('Borrow')">
                                <div class="input-group-append">
                                    <a href="#" @click="searchEmployee('Borrow')" class="btn font-weight-bold btn-success btn-icon">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div v-if="borrow_step_2 == 'current'" class="row" >
                        <div class="col-lg-12 mt-5">
                            <h5>Employee : {{ employee.first_name + ' ' + employee.last_name }}</h5>
                        </div>

                        <div class="col-lg-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                       <i class="fas fa-ticket-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" v-model="itemTicketNumber" placeholder="Ticket No.">
                            </div>
                        </div>

                        <div class="col-lg-12 mt-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                       <i class="fas fa-shopping-cart"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" v-model="itemName" placeholder="Search Item (Serial Number/Model)" @keyup.enter="searchBorrowItem">
                                <div class="input-group-append">
                                    <a href="#" @click="searchBorrowItem" class="btn font-weight-bold btn-success btn-icon">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12 mt-2" v-if="items">
                            <div class="table-responsive">
                                <table class="table table-bordered table-checkable" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">Serial No.</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in items" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button v-if="validateBorrowItem(item)" class="btn btn-md btn-primary" @click="addBorrowItem(item)">Select</button>
                                                <button v-else class="btn btn-md btn-primary" disabled>Selected</button>
                                            </td>
                                        </tr>
                                        <tr v-if="items.length == 0">
                                            <td colspan="4" align="center">No Items Found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-2" v-if="selectedItems.length > 0">
                            <h5>Selected Items ({{selectedItems.length}})</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-checkable" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">Serial No.</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in selectedItems" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button class="btn btn-md btn-danger" @click="removeBorrowItem(item)">Remove</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-5">
                            <button type="button" class="btn btn-warning btn-md float-left" @click="previousStep1Borrow">Back</button>
                            <button v-if="selectedItems.length > 0 && itemTicketNumber != ''" type="button" class="btn btn-primary btn-md float-right" @click="nextStep3Borrow">Proceed</button>
                            <button v-else type="button" class="btn btn-primary btn-md float-right" disabled>Proceed</button>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div v-if="borrow_step_3 == 'current'" class="row" >
                        <div class="col-lg-12 mt-2">
                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <tr>
                                    <td>Employee : </td>
                                    <td>{{ employee.first_name + ' ' + employee.last_name }}</td>
                                </tr>
                                <tr>
                                    <td>Ticket No. : </td>
                                    <td>{{ itemTicketNumber }}</td>
                                </tr>
                            </table>
                            <h5>Preview Items ({{selectedItems.length}})</h5>
                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <thead>
                                        <tr>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">Serial No.</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in selectedItems" :key="i" >
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.type}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.model}}</small></td>
                                        <td style="text-align: center; vertical-align: middle;"><small>{{item.serial_number}}</small></td>
                                    </tr>
                                    <tr v-if="selectedItems.length == 0">
                                        <td colspan="4" align="center">No Items Found</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>

                        <div class="col-lg-12 mt-5">
                            <button type="button" class="btn btn-warning btn-md float-left" @click="previousStep2Borrow">Back</button>
                            <button type="button" class="btn btn-primary btn-md float-right" @click="saveBorrowItems">Submit</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Item -->
    <div class="modal fade" id="return-item-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeLg" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Return Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="wizard wizard-1" id="kt_wizard" data-wizard-state="step-first" data-wizard-clickable="false">
                        <!--begin::Wizard Nav-->
                        <div class="wizard-nav border-bottom">
                            <div class="wizard-steps p-8 p-lg-10">
                                <!--begin::Wizard Step 1 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="return_step_1">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-search"></i>
                                        <h3 class="wizard-title">1. Search Employee</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 1 Nav-->
                                <!--begin::Wizard Step 2 Nav-->
                                <div class="wizard-step" data-wizard-type="step" :data-wizard-state="return_step_2">
                                    <div class="wizard-label">
                                        <i class="wizard-icon flaticon-list"></i>
                                        <h3 class="wizard-title">2. Select Items to Return</h3>
                                    </div>
                                    <span class="svg-icon svg-icon-xl wizard-arrow last">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Arrow-right.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24" />
                                                <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-90.000000) translate(-12.000000, -12.000000)" x="11" y="5" width="2" height="14" rx="1" />
                                                <path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </div>
                                <!--end::Wizard Step 2 Nav-->
                            </div>
                        </div>
                    </div>

                    <!-- Step 1 -->
                    <div v-if="return_step_1 == 'current'" class="row" >
                        <div class="col-lg-12 mt-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-address-card"></i>
                                    </span>
                                </div>
                                <input id="return_id_number" type="text" class="form-control" v-mask="mask" v-model="employeeIDNumber" placeholder="ID Number (XX-XXXXX-XXXX)" @keyup.enter="searchEmployee('Return')">
                                <div class="input-group-append">
                                    <a href="#" @click="searchEmployee('Return')" class="btn font-weight-bold btn-success btn-icon">
                                        <i class="fas fa-search"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div v-if="return_step_2 == 'current'" class="row" >
                        <div class="col-lg-12 mt-5">
                            <h5>Employee : {{ employee.first_name + ' ' + employee.last_name }}</h5>
                            <button class="btn btn-md btn-success float-right" @click="searchEmployee('Return')">Refresh</button>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <div class="table-responsive" v-if="employee.borrowed_items">
                                <table class="table table-bordered table-checkable" id="kt_datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">Serial No.</th>
                                            <th class="text-center">Ticket No.</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in employee.borrowed_items" :key="i" >
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.type}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.model}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.inventory_info.serial_number}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;"><small>{{item.ticket_number}}</small></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <button v-if="item.status == 'Borrowed'" class="btn btn-md btn-primary" @click="returnItem(item)">Return</button>
                                                <button v-else class="btn btn-md btn-primary" disabled>Returned</button>
                                            </td>
                                        </tr>
                                        <tr v-if="employee.borrowed_items.length == 0">
                                            <td colspan="4" align="center">No Items Found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-2">
                            <button type="button" class="btn btn-warning btn-md float-left" @click="previousStep1Return">Back</button>
                        </div>
                    </div>
                    
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
    </div>


</div>
</template>

<script>
import VueMask from 'v-mask'
Vue.use(VueMask)
export default {
    data() {
        return {

            mask: '##-#####-####',

            //Borrow
            borrow_step_1 : 'current',
            borrow_step_2 : '',
            borrow_step_3 : '',
            employeeIDNumber : '',
            employee : '',
            errors : [],

            //Items
            itemTicketNumber : '',
            itemName : '',
            items : '',
            selectedItems : [],

            //Return
            return_step_1 : 'current',
            return_step_2 : '',
        }
    },
    methods: {
        searchEmployee(method){
            let v = this;
            v.employee = '';
            if(v.employeeIDNumber){
                axios.get('/item-search-employee?id_number=' + v.employeeIDNumber)
                .then(response => { 
                    v.employee = response.data;
                    if(v.employee){
                        if(method == 'Borrow'){
                            v.borrow_step_1 = 'done';
                            v.borrow_step_2 = 'current';
                        }else if(method == 'Return'){
                            v.return_step_1 = 'done';
                            v.return_step_2 = 'current';
                        }
                        
                    }else{
                        Swal.fire('No Employee Found! Please try again', '', 'warning');
                    }
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            }else{
                Swal.fire('ID Number is required', '', 'error');
            }  
        },
        //Borrow Functions
        showBorrowScanItem() {
            let v = this;
            v.borrow_step_1 = 'current';
            v.borrow_step_2 = '';
            v.borrow_step_3 = '';
            $('#borrow-item-modal').modal('show');
            $('#borrow-item-modal').on('shown.bs.modal', function () {
                $('#borrow_id_number').focus();
            })
        },
        searchBorrowItem(){
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
        addBorrowItem(item){
            let v = this;
            v.selectedItems.push({
                id : item.id,
                model : item.model,
                serial_number : item.serial_number,
                type : item.type,
            });
            this.items.splice(item, 1);
        },
        removeBorrowItem(item){
            this.selectedItems.splice(item, 1);
        },
        validateBorrowItem(item){
            var index = this.selectedItems.findIndex(selected_item => selected_item.id == item.id);
            if(index === 0){
                return false
            }else{
                return true;
            }
        },
        previousStep1Borrow(){
            let v = this;
            v.borrow_step_1 = 'current';
            v.borrow_step_2 = '';
            v.borrow_step_3 = '';
        },
        previousStep2Borrow(){
            let v = this;
            v.borrow_step_2 = 'current';
            v.borrow_step_3 = '';
        },
        nextStep3Borrow(){
            let v = this;
            v.borrow_step_2 = 'done';
            v.borrow_step_3 = 'current';
        },
        saveBorrowItems(){
            let v = this;
            Swal.fire({
                title: 'Are you sure you want to save?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('employee_id', v.employee ? v.employee.id : "");
                    formData.append('ticket_number', v.itemTicketNumber ? v.itemTicketNumber : "");
                    formData.append('user_inventories', v.selectedItems ? JSON.stringify(v.selectedItems) : "");
                    axios.post(`/save-borrow-item`, formData)
                    .then(response =>{
                        if(response.data.status == "success"){
                            Swal.fire('User items has been saved! Total (' + response.data.save_count+')', '', 'success');  
                            Swal.fire({
                                title: 'Success!',
                                text: 'User items has been saved! Total (' + response.data.save_count+')',
                                icon: 'success',
                                confirmButtonText: 'Okay',
                                timer : 5000
                            }).then(okay => {
                                if (okay) {
                                    location.reload();
                                }
                            });      
                            
                        }else{
                            Swal.fire('Error: Cannot saved. Please try again.', '', 'error');
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    })
                }
            });
        },

        //Return Functions
        showReturnScanItem() {
            let v = this;
            v.return_step_1 = 'current';
            v.return_step_2 = '';
            $('#return-item-modal').modal('show');
            $('#return-item-modal').on('shown.bs.modal', function () {
                $('#return_id_number').focus();
            })
        },
        returnItem(item){
            let v = this;
            var index = this.employee.borrowed_items.findIndex(selected_item => selected_item.id == item.id);
            Swal.fire({
            title: 'Are you sure you want to return this item ( '+ item.inventory_info.model + ')?',
            icon: 'question',
            showDenyButton: true,
            confirmButtonText: `Yes`,
            denyButtonText: `No`,
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    formData.append('user_inventory_id', item ? item.id : "");
                    axios.post(`/save-return-item`, formData)
                    .then(response =>{
                        if(response.data.status == "success"){
                            Swal.fire('Item has been returned!', '' ,'success');  
                            this.employee.borrowed_items.splice(index,1,response.data.item);
                        }else{
                            Swal.fire('Error: Cannot returned. Please try again.', '', 'error');
                        }
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    })
                }
            })            
        },
        previousStep1Return(){
            let v = this;
            v.return_step_1 = 'current';
            v.return_step_2 = '';
        },

    },
}
</script>

<style lang="scss" scoped>

</style>