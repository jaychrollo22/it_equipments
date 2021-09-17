<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    <div class="d-flex flex-column">
                        <!--begin::Title-->
                        <h2 class="text-white font-weight-bold my-2 mr-5">RFID Registration Devices</h2>
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
            <div class="container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">List
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-success mr-2" @click="addDevice">New</button>
                            <button class="btn btn-primary mr-2" @click="showActivateImpinjDevice">Activate Device</button>
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
                                        <th class="text-center">READER NAME</th>
                                        <th class="text-center">MAC ADDRESS</th>
                                        <th class="text-center">TYPE</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredDeviceQueues" :key="i" >
                                       <td align="center"><small>{{item.reader_name}}</small></td>
                                       <td align="center"><small>{{item.mac_address}}</small></td>
                                       <td align="center"><small>{{item.type}}</small></td>
                                       <td align="center">
                                           <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editDevice(item)">
                                                <i class="flaticon-edit"></i>
                                            </button>
                                       </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row col-md-12" v-if="filteredDeviceQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Locations : {{ filteredDevices.length }} </span><br>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>  
        </div>  
    </div>

    <div class="modal fade" id="device-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center"></h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">{{ action }} Device</label> 
                                <input type="text" class="form-control" placeholder="Input here..." v-model="device.reader_name">
                                <span class="text-danger" v-if="errors.reader_name">{{ errors.reader_name[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Type</label> 
                               <select class="form-control" v-model="device.type">
                                   <option value="Impinj">Impinj</option>
                                   <option value="Geovision">Geovision</option>
                               </select>
                                <span class="text-danger" v-if="errors.type">{{ errors.type[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveDevice">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="activate-device-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center"></h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Activate Impinj Device</label> 
                               <select class="form-control" v-model="activateImpinjDevice">
                                   <option  v-for="(item, i) in impinj_devices" :key="i" :value="item.reader_name" >{{item.reader_name}}</option>
                               </select>
                                <span class="text-danger" v-if="errors.type">{{ errors.activateDevice[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveActivateImpinjDevice">Save</button>
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
                activateImpinjDevice : '',
                device : {
                    'id' : '',
                    'reader_name' : '',
                    'type' : ''
                },
                action : '',
                devices : [],
                impinj_devices : [],
                geovision_devices : [],
                errors : [],
                currentPage: 0,
                itemsPerPage: 10, 
            }
        },
        created () {
            this.getDevices();
            this.getActivatedImpinjDevice();
        },
        methods : {
            showActivateImpinjDevice(){
                $('#activate-device-modal').modal('show');
            },
            getActivatedImpinjDevice(){
                let v = this;
                v.activateImpinjDevice = '';
                axios.get('/rfid-registration-impinj-devices-activated-data')
                .then(response => { 
                    v.activateImpinjDevice = response.data.activate_impinj_device;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            saveActivateImpinjDevice(){
                let v = this;
                let formData = new FormData();
                formData.append('activate_impinj_device', v.activateImpinjDevice ? v.activateImpinjDevice : "");
                axios.post(`/rfid-registration-impinj-devices-activate`, formData)
                .then(response =>{
                    if(response.data){
                        Swal.fire('Device has been activated!', '', 'success');        
                        $('#activate-device-modal').modal('hide');
                        v.activateImpinjDevice = '';
                        this.getActivatedImpinjDevice();
                    }else{
                        Swal.fire('Error: Cannot changed. Please try again.', '', 'error');   
                    }
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            addDevice(){
                let v = this;
                v.errors = [];
                v.device.id = '';
                v.device.reader_name = '';
                v.device.type = '';
                v.action = 'New';
                $('#device-modal').modal('show');
            },
            editDevice(device){
                let v = this;
                v.errors = [];
                v.device.id = device.id;
                v.device.reader_name = device.reader_name;
                v.device.type = device.type;
                v.action = 'Update';
                $('#device-modal').modal('show');
            },
            saveDevice(){
                let v = this;
                Swal.fire({
                    title: 'Are you sure you want to save this device?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        var postURL = `/rfid-registration-devices-store`;
                        if(v.action == "Update"){
                            formData.append('id', v.device.id ? v.device.id : "");
                            postURL = `/rfid-registration-devices-update`;
                        }
                        formData.append('reader_name', v.device.reader_name ? v.device.reader_name : "");
                        formData.append('type', v.device.type ? v.device.type : "");
                        axios.post(postURL, formData)
                        .then(response =>{
                            if(response.data.status == "success"){
                                Swal.fire('Device has been saved!', '', 'success');        
                                $('#device-modal').modal('hide');
                                v.device.id = '';
                                v.device.reader_name = '';  
                                v.device.type = '';  
                                this.getDevices();
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
            getDevices() {
                let v = this;
                v.devices = [];
                v.impinj_devices = [];
                axios.get('/rfid-registration-devices-data')
                .then(response => { 
                    v.devices = response.data;
                    v.devices.forEach(e => {
                        if(e.type == 'Impinj'){
                            v.impinj_devices.push(e);
                        }
                        if(e.type == 'Geovision'){
                            v.geovision_devices.push(e);
                        }
                    });
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
            filteredDevices(){
                let self = this;
                if(self.devices){
                    return Object.values(self.devices).filter(item => {
                        return item.reader_name.toLowerCase().includes(this.keywords.toLowerCase())
                    });
                }else{
                    return [];
                }
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredDevices).length / this.itemsPerPage)
            },
            filteredDeviceQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredDevices.slice(index, index + this.itemsPerPage);
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

</style>