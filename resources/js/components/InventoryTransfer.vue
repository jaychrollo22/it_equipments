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
                        <h2 class="text-white font-weight-bold my-2 mr-5">Transfer</h2>
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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Asset Transfer</a>
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
                            <h3 class="card-label">List
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-primary mr-2" @click="newTransfer">New</button>
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
                                        <th class="text-center">Transfer Code</th>
                                        <th class="text-center">Date of Transfer</th>
                                        <th class="text-center">Date Requested</th>
                                        <th class="text-center">Location</th>
                                        <th class="text-center">Requested By</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Requested Name</label> 
                                <input type="text" class="form-control" placeholder="Requested Name" v-model="transfer.requested_by">
                                <span class="text-danger" v-if="errors.requested_by">{{ errors.requested_by[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Department</label> 
                                <input type="text" class="form-control" placeholder="Department" v-model="transfer.transfer_department">
                                <span class="text-danger" v-if="errors.transfer_department">{{ errors.transfer_department[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Company</label> 
                                <input type="text" class="form-control" placeholder="Company" v-model="transfer.transfer_company">
                                <span class="text-danger" v-if="errors.transfer_company">{{ errors.transfer_company[0] }}</span>
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
                    </div>
                    <h5>Select Items to Transfer</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="kt_datatable">
                            <thead>
                                <tr>
                                   <th class="text-center">Type</th>
                                    <th class="text-center">Model</th>
                                    <th class="text-center">Serial No.</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
                action : '',
                transfer : {
                    requested_by : '',
                    transfer_department : '',
                    transfer_company : '',
                    date_requested : '',
                },
                errors : []
            }
        },
        methods: {
            newTransfer() {
                let v = this;
                v.action = 'New';
                $('#transfer-modal').modal('show');
            }
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