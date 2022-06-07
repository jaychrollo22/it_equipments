<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Generate Letter of Undertaking</h2>
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
                        <div class="card-toolbar">
                            <!-- <button class="btn btn-primary mr-2" @click="preview">Preview</button> -->
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
                                            <td><small>{{user_inventory.inventory_info.type}}</small> </td>
                                            <td><small>{{user_inventory.inventory_info.model}}</small> </td>
                                            <td><small>{{user_inventory.inventory_info.serial_number}}</small> </td>
                                            <td><small>{{user_inventory.inventory_info.id}}</small></td>
                                        </tr>
                                        <tr>
                                            <td><small>Processor</small></td>
                                            <td><small>{{user_inventory.inventory_info.processor}}</small> </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                <b>B.	Operating System Installation:</b>
                                <button class="btn btn-primary btn-sm mb-2 float-right" @click="addNewOSIntallation">Add New</button>
                                <table class="table table-bordered">
                                    <thead>
                                        <th>Software</th>
                                        <th>Version</th>
                                        <th>Serial Number</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row,index) in os_installations" v-bind:key="index">
                                            <td>
                                                <input class="form-control" placeholder="Software" type="text" v-model="row.software">
                                            </td>
                                            <td>
                                                <input class="form-control" placeholder="Version" type="text" v-model="row.version">
                                            </td>
                                            <td>
                                                <input class="form-control" placeholder="Serial Number" type="text" v-model="row.serial_number">
                                            </td>
                                            <td align="center">
                                                <button type="button" class="btn btn-danger btn-sm mt-2" @click="removeOSIntallation(index)">Remove</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <b>Inclusions</b>
                                <textarea class="form-control" id="" cols="30" rows="3" placeholder="Type Here.." v-model="inclusions"></textarea>
                                <br>
                            </div>
                            <b>RECEIVED BY: {{user_inventory.employee_info.first_name + ' ' + user_inventory.employee_info.last_name }}</b>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" @click="generateLOU">Generate</button>
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
                errors : [],
            }
        },
        created () {
            this.getUserInventory();
        },
        methods: {
            generateLOU(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to generate this ?',
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