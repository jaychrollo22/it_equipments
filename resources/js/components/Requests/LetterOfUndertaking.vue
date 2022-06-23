<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <h2 class="text-white font-weight-bold my-2 mr-5">Letter of Undertaking</h2>
                        </div>
                    </div>
                </div>
            </div>
             <div class="d-flex flex-column-fluid">
                <div class="container">
                    <div class="card card-custom card-border">
                        <div class="card-header">
                            <div class="card-title">
                                <span class="card-icon">
                                    <i class="flaticon2-mail text-primary"></i>
                                </span>
                               
                                <small>Employee Acceptance</small>
                            </div>
                            <div class="card-toolbar">
                                <div v-if="currentUser.user.user_role.role == 'Administrator' || currentUser.user.user_role.role == 'IT Support'">
                                    <button v-if="borrow_request.acceptance == '' || borrow_request.acceptance == null" class="btn btn-md btn-warning mr-2 mt-1" title="Notify User" @click="notifyUserForAcceptance" :disabled="notifDisable"><i class="fa flaticon2-telegram-logo"></i> {{ borrow_request.notify == '1' ? 'Sent' : '' }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <center>
                                <h4>L E T T E R&nbsp;&nbsp;&nbsp;O F&nbsp;&nbsp;&nbsp;U N D E R T A K I N G</h4>
                                
                            </center>
                            <br>
                            <p style="font-size:15px;">
                                KNOW ALL MEN BY THESE PRESENTS:
                            </p>
                            <br>
                            <p style="font-size:15px;text-indent:50px;word-wrap: break-word;">
                                This Undertaking is executed by <strong>{{borrow_request.employee.first_name + ' ' + borrow_request.employee.last_name}}</strong> herein referred to as END USER, in favor <strong>{{borrow_request.employee.companies[0].name}}</strong>, a corporation duly organized and existing under Philippine law, with address at <strong>{{borrow_request.employee.locations[0].address[0].name}}</strong>, the following terms and conditions:
                            </p>
                            
                            <strong>Computer Identification: </strong> <br>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">MODEL</th>
                                        <th class="text-center">SERIAL NUMBER</th>
                                        <th class="text-center">TYPE</th>
                                        <th class="text-center">PROCESSOR</th>
                                        <th class="text-center">OS AND VERSION</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, i) in borrow_request.borrow_request_items" :key="i" >
                                            <td class="text-center">{{item.inventory_info.id}}</td>
                                            <td class="text-center">{{item.inventory_info.model}}</td>
                                            <td class="text-center">{{item.inventory_info.serial_number}}</td>
                                            <td class="text-center">{{item.inventory_info.type}}</td>
                                            <td class="text-center">{{item.inventory_info.processor}}</td>
                                            <td class="text-center">{{item.inventory_info.os_name_and_version}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>CONFORME:</strong> </td>
                                    <td>{{borrow_request.employee.first_name + ' ' + borrow_request.employee.last_name}}</td>
                                    <td><strong>DATE:</strong></td>
                                    <td>{{borrow_request.acceptance_date}}</td>
                                </tr>
                            </table>

                            
                       

                        </div>
                        <div class="card-footer text-center">
                            <div v-if="currentUser.user_id == borrow_request.user_id">
                                <button v-if="borrow_request.acceptance == '1'" class="btn btn-md btn-primary" disabled>Accepted</button>
                                <button v-else class="btn btn-md btn-primary" @click="acceptBorrowRequest" :disabled="acceptDisable">Accept</button>
                            </div>
                            <div v-else>
                                 <button v-if="borrow_request.acceptance == '1'" class="btn btn-md btn-primary" disabled>Accepted</button>
                                 <button v-if="borrow_request.acceptance == '1'" class="btn btn-md btn-warning" disabled>For Acceptance</button>
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
                currentUser: '',
                borrow_request : '',
                acceptDisable: false,

                notifDisable:false,
            }
        },
        created () {
            this.getBorrowRequestData();
            this.getCurrentUser();
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
            notifyUserForAcceptance(){
                let v = this;
                this.notifDisable = true;
                Swal.fire({
                title: 'Are you sure you want to sent this letter of undertaking to assignee for acceptance?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.borrow_request.id);
                        axios.post(`/letter-of-undetaking-notification`, formData)
                        .then(response =>{
                            if(response.data.status=='sent'){
                                Swal.fire('For acceptance has been sent. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            this.notifDisable = false;
                                            this.getBorrowRequestData();
                                        }
                                    });
                            }else{
                                Swal.fire('Error: Not sent. Please try again.', '', 'error');  
                                this.notifDisable = false; 
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                            this.notifDisable = false;
                        })
                    }else{
                        this.notifDisable = false;
                    }
                })
            },
            acceptBorrowRequest(){
                let v = this;
                Swal.fire({
                title: 'Are you sure you want to accept this Letter of Undertaking?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('id', v.borrow_request.id);
                        axios.post(`/letter-of-undetaking-accept`, formData)
                        .then(response =>{
                            if(response.data.status=='saved'){
                                Swal.fire('Borrow request has been accepted. Thank you.', '', 'success')
                                    .then(okay => {
                                        if (okay) {
                                            $('#for-approval-modal').modal('hide');
                                            this.getBorrowRequestData();  
                                        }
                                    });
                            }else{
                                Swal.fire('Error: Cannot saved. Please try again.', '', 'error');   
                            }
                        })
                        .catch(error => {
                            v.errors = error.response.data.errors;
                        })

                    }
                })
            },
            getBorrowRequestData() {
                let v = this;
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                var request_number = urlParams.get('request_number');
                v.borrow_request = '';
                axios.get('/letter-of-undertaking-data?request_number='+request_number)
                .then(response => { 
                    if(response.data){
                        v.borrow_request = response.data;
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