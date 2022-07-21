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
                                <button class="btn btn-md btn-success mr-2" @click="scanGatePassQR"> <i class="fa fa-barcode"></i> GATE PASS</button>
                                <button class="btn btn-md btn-primary" @click="scanQR"> <i class="fa fa-barcode"></i> SCAN ASSET QR</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row" v-if="inventory">
                                <h4>Results</h4>
                                <div v-if="inventory" class="table-responsive">
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
                            <div class="row" v-if="gate_pass">
                                <h4>Gate Pass</h4>
                                <div v-if="gate_pass.gate_pass" class="table-responsive">
                                    <button v-if="gate_pass.gate_pass.guard_on_duty == '' || gate_pass.gate_pass.guard_on_duty == null" class="btn btn-info float-right mb-2" @click="checkBy">Check</button>
                                    <button v-else disabled class="btn btn-info float-right mb-2">Checked</button>
                                    <iframe id="id-frame" :src="'/print-gate-pass/'+gate_pass.gate_pass.id + '#page=1&scrollbar=1&toolbar=1&navpanes=1'" frameborder="0" width="100%" height="700px"></iframe>
                                </div>
                            </div>
                            <!-- <div v-if="inventory != null || gate_pass != null">
                                <center>
                                    <h1 class="text-default" style="cursor:pointer" @click="scanQR">Click Here to Scan</h1>
                                </center>
                            </div> -->
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
                        <button type="button" class="btn btn-info btn-round btn-fill btn-lg mt-4" @click="switchCamera"> SWITCH CAMERA ({{camera}})</button>
                        <button type="button" class="btn btn-danger btn-round btn-fill btn-lg mt-4" @click="closeScan">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gate Pass Modal -->
        <div class="modal fade" id="gate-pass-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-view" role="document">
                <div class="modal-content modal-content-view">
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">GATE PASS SCANNER</h2>
                    </div>
                    <div class="modal-body modal-body-view">
                        <qrcode-stream v-if="gatePassStatusQr" :camera="gate_pass_camera" @decode="onDecodeGatePassQR" @init="logErrors" style="height:300px;border:5px solid red;padding:5px 5px 5px 5px;width:100%;"/>

                        <hr>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    #
                                </span>
                            </div>
                            <input id="inventory_id" type="text" class="form-control" v-model="gate_pass_id" placeholder="Type Gate Pass QR(optional)" @keyup.enter="onDecodeGatePassQR(gate_pass_id)">
                            <div class="input-group-append">
                                <a href="#" @click="onDecodeGatePassQR(gate_pass_id)" class="btn font-weight-bold btn-success btn-icon">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                        </div>
                    

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info btn-round btn-fill btn-lg mt-4" @click="switchGatePassCamera"> SWITCH CAMERA ({{gate_pass_camera}})</button>
                        <button type="button" class="btn btn-danger btn-round btn-fill btn-lg mt-4" @click="closeGatePassScan">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Gate Pass / Check By Modal -->
        <div class="modal fade" id="gate-pass-check-by-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md modal-dialog-view" role="document">
                <div class="modal-content modal-content-view">
                    <div class="modal-header">
                        <h2 class="col-12 modal-title text-center">GATE PASS</h2>
                    </div>
                    <div class="modal-body modal-body-view">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Carrier/Hauler</label> 
                                    <input type="text" class="form-control" placeholder="Input Name.." v-model="gate_pass_check.carrier_by">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="role">Guard on Duty</label> 
                                    <input type="text" class="form-control" placeholder="Input Name.." v-model="gate_pass_check.guard_on_duty">
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info btn-round btn-fill btn-lg mt-4" @click="saveCheckGatePass"> Check & Save</button>
                        <button type="button" class="btn btn-danger btn-round btn-fill btn-lg mt-4" @click="closeGatePassCheck">Close</button>
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

                gate_pass_id: '',
                gate_pass : '',
                gate_pass_camera: 'front',
                gatePassStatusQr : false,

                gate_pass_check : {
                    id : '',
                    guard_on_duty : '',
                },
                checkSaveDisable : false

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
            // async onInit (promise) {
            //     try {
            //         await promise
            //     } catch (error) {
            //         const triedFrontCamera = this.camera === 'front'
            //         const triedRearCamera = this.camera === 'rear'

            //         const cameraMissingError = error.name === 'OverconstrainedError'

            //         if (triedRearCamera && cameraMissingError) {
            //         this.noRearCamera = true
            //         }

            //         if (triedFrontCamera && cameraMissingError) {
            //         this.noFrontCamera = true
            //         }

            //         console.error(error)
            //     }
            // },
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
            scanGatePassQR(){
                $('#gate-pass-modal').modal('show');
                this.gatePassStatusQr = true;
            },
            closeGatePassScan(){
                this.gate_pass_id = '';
                this.gatePassStatusQr = false;
                $('#gate-pass-modal').modal('hide');
            },
            switchGatePassCamera() {
                switch (this.gate_pass_camera) {
                    case 'front':
                    this.gate_pass_camera = 'rear'
                    break
                    case 'rear':
                    this.gate_pass_camera = 'front'
                    break
                }
            },
            onDecodeGatePassQR(result){
                let v = this;
                axios.get('/gate-pass-data?id='+result)
                .then(response => { 
                    if(response.data.status == 'success'){
                        v.gate_pass = response.data;
                        v.closeGatePassScan();
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
            checkBy(){
                let v = this;
                v.gate_pass_check.id = v.gate_pass.gate_pass.id;
                v.gate_pass_check.guard_on_duty = "";
                $('#gate-pass-check-by-modal').modal('show');
                
            },
            closeGatePassCheck(){
                let v = this;
                v.gate_pass_check.id = "";
                v.gate_pass_check.guard_on_duty = "";
                $('#gate-pass-check-by-modal').modal('hide');
            },
            saveCheckGatePass(){
                let v = this;
                v.checkSaveDisable = true;
                Swal.fire({
                title: 'Are you sure you want to check & save this gate pass?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.gate_pass_check.id ? v.gate_pass_check.id : "");
                        
                        formData.append('guard_on_duty', v.gate_pass_check.guard_on_duty ? v.gate_pass_check.guard_on_duty : "");
                        formData.append('carrier_by', v.gate_pass_check.carrier_by ? v.gate_pass_check.carrier_by : "");

                        axios.post(`/save-check-gate-pass`, formData)
                        .then(response =>{
                            if(response.data.status=='success'){
                                v.checkSaveDisable = false;
                                Swal.fire('Gate Pass has been checked!', '', 'success')
                                .then(okay => {
                                    if (okay) {
                                        this.onDecodeGatePassQR(v.gate_pass_check.id);
                                        $('#gate-pass-check-by-modal').modal('hide');
                                        document.getElementById('id-frame').contentWindow.location.reload();

                                    }
                                });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                                v.checkSaveDisable = false;
                            }
                        })
                        .catch(error => {
                            v.checkSaveDisable = false;
                            v.errors = error.response.data.errors;
                        })
                    }else if (result.isDenied) {
                        Swal.fire('Gate pass not saved', '', 'info');
                        v.checkSaveDisable = false;
                    }
                })  
            }
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
    }

    .modal-body-view {
        max-height: 90vh!important;
    }
</style>