<template>
    <div>
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                    <div class="d-flex align-items-center flex-wrap mr-1">
                        <div class="d-flex flex-column">
                            <!-- <h2 class="text-white font-weight-bold my-2 mr-5">Letter of Undertaking</h2> -->
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
                            <div class="card-toolbar" v-if="currentUser.user">
                                <div v-if="currentUser.user.user_role">
                                    <div v-if="currentUser.user.user_role.role == 'Administrator' || currentUser.user.user_role.role == 'IT Support'">
                                        <button v-if="borrow_request.acceptance == '' || borrow_request.acceptance == null" class="btn btn-md btn-warning mr-2 mt-1" title="Notify User" @click="notifyUserForAcceptance" :disabled="notifDisable"><i class="fa flaticon2-telegram-logo"></i> {{ borrow_request.notify == '1' ? 'Sent' : '' }}</button>
                                    </div>
                                </div>

                               <span v-if="borrow_request.acceptance == '1'" class="card-icon" @click="print" style="cursor:pointer">
                                    <i class="flaticon2-printer text-primary"></i>
                                </span>
                            </div>
                        </div>
                        <div class="card-body" v-if="borrow_request">
                            <center>
                                <h4>L E T T E R&nbsp;&nbsp;&nbsp;O F&nbsp;&nbsp;&nbsp;U N D E R T A K I N G</h4>
                                
                            </center>
                            <br>
                            <p style="font-size:15px;color:black">
                                KNOW ALL MEN BY THESE PRESENTS:
                            </p>
                            <br>
                            <p style="font-size:15px;text-indent:50px;word-wrap: break-word;color:black">
                                This Undertaking is executed by <strong>{{borrow_request.employee.first_name + ' ' + borrow_request.employee.last_name}}</strong> herein referred to as END USER, in favor <strong>{{borrow_request.employee.companies[0].name}}</strong>, a corporation duly organized and existing under Philippine law, with address at <strong>{{borrow_request.employee.locations[0].address[0].name}}</strong>, the following terms and conditions:
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                1.  I understand that the Asset issued is solely for official use only.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                2.	I understand that I am responsible for the Asset issued to me and that I will care for the equipment in such a manner as to prevent loss or damage.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                3.	The Asset should be transported in its case and stored carefully so it is not susceptible to damage. I may not make any permanent personally identifying marks on the Asset including adhesive labels/stickers.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                4.	Storing (creating, downloading, saving) of personal files (documents, photos, videos) that are not work-related is prohibited (Desktop/Laptop).
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                5.	The Asset should never be left unattended after office hours, weekends, holidays, etc.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                6.	I shall be fully accountable for theft, loss or damage of the property.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                7.	Acceptable storage of Asset (etc. Laptop) during office hours includes locked desks, cabinets or other secured spaces not visible when the Asset is not in the user’s possession.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                8.	In case of any malfunction, I am required to report to the IT Department.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                9.	In case of loss or theft, I must report the incident to the nearest police station.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                10.	I may not take the Asset for repair to any external agency or vendor at any point of time.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                11.	I shall be liable to pay all charges incurred due to unofficial use of the unit or damage: <br>
                                <ul>
                                    <li>Payable parts: <i style="font-size:15px;color:black">Keyboard, LCD Screen/Display Assembly, Palm Rest, Motherboard, Laptop Charger, center control panel and other parts.</i> </li>
                                </ul>
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                12.	IT Department may borrow the unit anytime with or without notice due to security inspection.
                            </p>
                            <p class="ml-2" style="font-size:15px;word-wrap: break-word;color:black">
                                13.	I attest that the Asset with the following specifications have been turned over to me in good condition:
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

                            <p class="ml-2 mt-2" style="font-size:15px;word-wrap: break-word;color:black">
                                14.	I agree that I shall surrender the unit and all its accessories mentioned herein upon her separation with LAFIL or upon demand by the latter even without prior advice.
                            </p>

                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>CONFORME:</strong> </td>
                                    <td>{{borrow_request.employee.first_name + ' ' + borrow_request.employee.last_name}} <br><small>(Assignee)</small></td>
                                    <td><strong>DATE:</strong></td>
                                    <td>{{borrow_request.acceptance_date}}</td>
                                </tr>
                            </table>

                            <i><small class="float-left">For ITD use do not write anything</small></i>
                            <table class="table table-bordered">
                                
                                <tr>
                                    <td>Received by: <span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span></td>
                                    <td>Date: </td>
                                </tr>
                                <tr>
                                    <td colspan="2">Remarks: </td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer text-center" v-if="borrow_request">
                            <div v-if="currentUser.user_id == borrow_request.user_id">
                                <button v-if="borrow_request.acceptance == '1'" class="btn btn-md btn-primary" disabled>Accepted</button>
                                <button v-else class="btn btn-md btn-primary" @click="acceptBorrowRequest" :disabled="acceptDisable">Accept</button>
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
            print(){
                window.print();
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