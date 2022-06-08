<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Print Letter of Undertaking</h2>
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
                            LETTER OF UNDERTAKING
                            <span class="d-block text-muted pt-2 font-size-sm"></span></h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row" v-if="user_inventory"> 
                            <div class="table-responsive">
                                <b>A.	Computer Identification:</b>
                                <table class="table table-bordered">
                                    <thead>
                                        <th>Device</th>
                                        <th>Model</th>
                                        <th>Serial Number</th>
                                        <th>ITD Tag</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><small><strong>{{user_inventory.inventory_info.type}}</strong></small> </td>
                                            <td><small>{{user_inventory.inventory_info.model}}</small> </td>
                                            <td><small>{{user_inventory.inventory_info.serial_number}}</small> </td>
                                            <td><small>{{user_inventory.inventory_info.id}}</small></td>
                                        </tr>
                                        <tr>
                                            <td><small><strong>Processor</strong></small></td>
                                            <td colspan="3"><small>{{user_inventory.inventory_info.processor}}</small> </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                <b>B.	Operating System Installation:</b>
                                <table class="table table-bordered">
                                    <thead>
                                        <th colspan="3">Software and OS Version</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3"><small>{{user_inventory.inventory_info.os_name_and_version}}</small> </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <b>Inclusions</b>
                                <textarea class="form-control" id="" cols="30" rows="3" placeholder="Type Here.." v-model="inclusions"></textarea>
                                <br>
                            </div>
                            <b>RECEIVED BY: {{user_inventory.employee_info.first_name + ' ' + user_inventory.employee_info.last_name }}</b>

                            <div class="table-responsive mt-5">
                                <table class="table table-bordered" v-if="letter_of_undertaking_id">
                                    <tr>
                                        <td><small>Upload Signed LOU</small></td>
                                        <td>
                                            <input type="file" id="attachment" class="form-control" ref="file" accept="*" v-on:change="fileHandleFileUpload()">
                                        </td>
                                        <td align="center">
                                            <button class="btn btn-sm btn-primary" @click="saveLOUAttachment" :disabled="save_disable">Save</button>
                                            <a v-if="letter_of_undertaking_file" :href="'/storage/letter_of_undertaking_attachments/'+letter_of_undertaking_file" target="_blank" class="btn btn-sm btn-success">View</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" @click="generateLOU">Print</button>
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
                os_installations: [{
                     software : '',
                    version : '',
                    serial_number : ''
                }],
                user_inventory : '',
                inclusions : '',
                letter_of_undertaking_id : '',
                letter_of_undertaking_file : '',
                errors : [],
                uploaded_scanned_file : '',
                save_disable : false,
            }
        },
        created () {
            this.getUserInventory();
        },
        methods: {
            saveLOUAttachment(){
                this.save_disable = true;
                Swal.fire({
                title: 'Are you sure you want to upload this attachment?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('letter_of_undertaking_id', this.letter_of_undertaking_id ? this.letter_of_undertaking_id : "");
                        formData.append('uploaded_scanned_file', this.uploaded_scanned_file ? this.uploaded_scanned_file : "");
                        axios.post(`/save-lou-attachment`, formData)
                        .then(response =>{
                            if(response.data.status == 'saved'){
                                Swal.fire('Attachment has been uploaded', '', 'success')
                                .then(okay => {
                                    if (okay) {
                                        this.getUserInventory();
                                        this.save_disable = false;
                                    }
                                })
                            }else{
                                this.save_disable = false;
                            }
                        })
                    }else{
                        this.save_disable = false;
                    }
                })
            },
            fileHandleFileUpload(){
                var file = document.getElementById("attachment");
                this.uploaded_scanned_file = file.files[0];
            },
            generateLOU(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to generate this Letter of Under?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('user_inventory_id', v.user_inventory.id ? v.user_inventory.id : "");
                        formData.append('os_installations', v.os_installations ? JSON.stringify(v.os_installations) : "");
                        formData.append('inclusions', v.inclusions ? v.inclusions : "");
                        axios.post(`/save-generate-letter-of-undertaking`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                                Swal.fire('Letter of Undertaking has been generated. Thank you.', '', 'success')
                                    .then(okay => {
                                    if (okay) {
                                        // this.getUserInventory();
                                        // window.location.replace = "/print-generate-letter-of-undertaking?id="+response.data.letter_of_undertaking.id;
                                        var url = '/print-generate-letter-of-undertaking?id='+response.data.letter_of_undertaking.id;
                                        location.replace(url)
                                    }
                                });
                            }
                        })
                        .catch(error => { 
                            v.errors = error.response.data.error;
                        })
                    }
                })

            },
            removeOSIntallation(index){
                this.os_installations.splice(index, 1);  
            },
            addNewOSIntallation(){
                this.os_installations.push({
                    software : '',
                    version : '',
                    serial_number : ''
                });
            },
            getUserInventory() {
                let v = this;
                axios.get('/get-lou-user-inventory')
                .then(response => { 
                    v.user_inventory = response.data;
                    if(v.user_inventory.letter_of_undertaking){
                        this.os_installations = v.user_inventory.letter_of_undertaking.os_installations ? JSON.parse(v.user_inventory.letter_of_undertaking.os_installations) : "";
                        this.inclusions = v.user_inventory.letter_of_undertaking.inclusions ? v.user_inventory.letter_of_undertaking.inclusions : "";
                        this.letter_of_undertaking_id = v.user_inventory.letter_of_undertaking.id ? v.user_inventory.letter_of_undertaking.id : "";
                        this.letter_of_undertaking_file = v.user_inventory.letter_of_undertaking.uploaded_scanned_file ? v.user_inventory.letter_of_undertaking.uploaded_scanned_file : "";
                    }
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            }
        },
    }
</script>

<style lang="scss" scoped>

</style>