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
                               
                                <small></small>
                            </div>
                            <div class="card-toolbar" v-if="employee">
                               <span class="card-icon" @click="print" style="cursor:pointer">
                                    <i class="flaticon2-printer text-primary"></i>
                                </span>
                            </div>
                        </div>
                        <div class="card-body" v-if="employee">
                            <center>
                                <h4>L E T T E R&nbsp;&nbsp;&nbsp;O F&nbsp;&nbsp;&nbsp;U N D E R T A K I N G</h4>
                                
                            </center>
                            <br>
                            <p style="font-size:15px;color:black">
                                KNOW ALL MEN BY THESE PRESENTS:
                            </p>
                            <br>
                            <p style="font-size:15px;text-indent:50px;word-wrap: break-word;color:black">
                                This Undertaking is executed by <strong>{{employee.first_name + ' ' + employee.last_name}}</strong> herein referred to as END USER, in favor <strong>{{employee.companies[0].name}}</strong>, a corporation duly organized and existing under Philippine law, with address at <strong>{{employee.locations[0].address[0].name}}</strong>, the following terms and conditions:
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
                                        <tr v-for="(item, i) in employee.borrowed_items" :key="i" >
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
                                    <td>{{employee.first_name + ' ' + employee.last_name}} <br><small>(Assignee)</small></td>
                                    <td><strong>DATE:</strong> </td>
                                    <td>{{dateNow()}}</td>
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
                employee: '',
                errors : [],
            }
        },
        created () {
            this.getLetterOfUndertakingData();
        },
        methods: {
            dateNow(){
                return moment().format("YYYY-MM-DD");
            },
            print(){
                window.print();
            },
            getLetterOfUndertakingData() {
                let v = this;
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                var employee_id = urlParams.get('id');
                v.employee = '';
                axios.get('/reports-letter-of-undertaking-print-data?id='+employee_id)
                .then(response => { 
                    if(response.data){
                        v.employee = response.data;
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