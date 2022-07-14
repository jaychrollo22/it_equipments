<template>
    <div>
        
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">QR SCANNER</h2>
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
                                <h3 class="card-label">
                                IT ASSET & INVENTORY
                                <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                            </div>
                            <div class="card-toolbar">
                                <button class="btn btn-md btn-primary" @click="scanQR"> <i class="fa fa-barcode"></i> SCAN QR</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row" v-if="inventory">
                                <h4>Results</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td align="right"><strong>ID</strong></td>
                                            <td><strong>{{inventory.id}}</strong> </td>
                                        </tr>
                                        <tr>
                                            <td align="right"><strong>SERIAL NO.</strong></td>
                                            <td>{{inventory.serial_number}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right"><strong>MODEL</strong></td>
                                            <td>{{inventory.model}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right"><strong>TYPE</strong></td>
                                            <td>{{inventory.type}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right"><strong>STATUS</strong></td>
                                            <td>
                                                <span v-if="inventory.is_borrowed" :class="getColorIsBorrow(inventory)" style="cursor:pointer" :title="inventory.is_borrowed.status">{{ inventory.is_borrowed.is_assigned == 'true' ? "Assigned" : inventory.is_borrowed.status }}</span>
                                                <span v-else-if="inventory.is_transfer" class="label label-primary label-pill label-inline mr-2" style="cursor:pointer">For Transfer</span>
                                                <span v-else-if="inventory.status == 'Active'" class="label label-success label-pill label-inline mr-2" title="Available to Borrow" style="cursor:pointer">Available</span>
                                                <span v-else class="label label-default label-pill label-inline mr-2" title="Available to Borrow" style="cursor:pointer">{{inventory.status}}</span>
                                            </td>
                                        </tr>
                                        <tr >
                                            <td align="right"><strong>ASSIGNEE</strong> </td>
                                            <td v-if="inventory.is_borrowed">
                                                <strong class="text-success">{{inventory.is_borrowed.employee_info.first_name + ' ' + inventory.is_borrowed.employee_info.last_name}}</strong>
                                            </td>
                                            <td v-else>
                                                <strong class="text-danger">Unassigned</strong>
                                            </td>
                                        </tr>
                                        <tr v-if="inventory.is_borrowed">
                                            <td align="right"><strong>DATE</strong> </td>
                                            <td>
                                                <strong>{{inventory.is_borrowed.borrow_date}}</strong>
                                            </td>
                                        </tr>
                                        <tr v-if="inventory.is_borrowed">
                                            <td v-if="inventory.is_borrowed.validity_end_date" align="right"><strong>VALIDITY DATE</strong> </td>
                                            <td v-if="inventory.is_borrowed.validity_end_date">
                                                <strong>{{inventory.is_borrowed.validity_end_date}}</strong>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div v-else>
                                <center>
                                    <h1 class="text-default" style="cursor:pointer" @click="scanQR">Click Here to Scan</h1>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanner/Camera Modal -->
        <div class="modal fade" id="qr-scanner-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-view" role="document">
                <div class="modal-content modal-content-view">
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">QR SCANNER</h2>
                    </div>
                    <div class="modal-body modal-body-view">
                        <qrcode-stream v-if="scanStatusQr" :camera="camera" @decode="onDecodeQR" @init="logErrors" style="height:300px;border:5px solid red;padding:5px 5px 5px 5px;width:100%;"/>

                        <hr>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    #
                                </span>
                            </div>
                            <input id="inventory_id" type="text" class="form-control" v-model="inventory_id" placeholder="Type QR ID (optional)" @keyup.enter="onDecodeQR(inventory_id)">
                            <div class="input-group-append">
                                <a href="#" @click="onDecodeQR(inventory_id)" class="btn font-weight-bold btn-success btn-icon">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                        </div>
                    

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-round btn-fill btn-lg mt-4" @click="switchCamera"> SWITCH CAMERA ({{camera}})</button>
                        <button type="button" class="btn btn-danger btn-round btn-fill btn-lg mt-4" @click="closeScan">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import { QrcodeStream } from 'vue-qrcode-reader'
    export default {
        components: { QrcodeStream },
        data() {
            return {
                inventory_id: '',
                inventory: '',
                scan_qr : '',
                camera: 'front',
                errors : '',
                scanStatusQr : false,
            }
        },    
        methods: {
            getColorIsBorrow(item){
                if(item.is_borrowed.is_assigned == 'true'){
                    return 'label label-primary label-pill label-inline mr-2';
                }else{
                    return 'label label-warning label-pill label-inline mr-2';
                }
            },
            logErrors (promise) {
                promise.catch(console.error)
            },
            switchCamera() {
                switch (this.camera) {
                    case 'front':
                    this.camera = 'rear'
                    break
                    case 'rear':
                    this.camera = 'front'
                    break
                }
            },
            async onInit (promise) {
                try {
                    await promise
                } catch (error) {
                    const triedFrontCamera = this.camera === 'front'
                    const triedRearCamera = this.camera === 'rear'

                    const cameraMissingError = error.name === 'OverconstrainedError'

                    if (triedRearCamera && cameraMissingError) {
                    this.noRearCamera = true
                    }

                    if (triedFrontCamera && cameraMissingError) {
                    this.noFrontCamera = true
                    }

                    console.error(error)
                }
            },
            scanQR(){
                $('#qr-scanner-modal').modal('show');
                this.scan_qr = '';
                this.scanStatusQr = true;
            },
            closeScan(){
                this.inventory_id = '';
                this.scan_qr = '';
                this.scanStatusQr = false;
                $('#qr-scanner-modal').modal('hide');
                
            },
            onDecodeQR(result) {
                let v = this;
                axios.get('/qr-scanner-data?id='+result)
                .then(response => { 
                    if(response.data.status == 'success'){
                        v.inventory = response.data.inventory;
                        v.closeScan();
                    }else{
                        Swal.fire({
                            title: 'Warning!',
                            text: 'No records found. Please try again. Thank you.',
                            icon: 'warning',
                            confirmButtonText: 'Okay'
                        }).then(okay => {
                            if (okay) {
                                this.refresh();
                            }
                        });
                    }
                    
                })
                .catch(error => { 
                    console.log(error);
                    v.errors = error.response.data.error;
                })
            },
            refresh(){
                location.reload();
            },
        },
    }
</script>

<style lang="scss" scoped>
    .error {
        font-weight: bold;
        color: red;
    }

    .modal-dialog-view {
        margin: 2.5vh auto!important;
    }
    .modal-content-view {
        max-height: 95vh!important;
        overflow: scroll!important;
    }

    .modal-body-view {
        max-height: 90vh!important;
    }
</style>