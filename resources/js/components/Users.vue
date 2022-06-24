<template>
<div>
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Users</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a>
                            <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">User Roles</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="card card-custom gutter-b">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-title">
                            <h3 class="card-label">Users List
                            <span class="d-block text-muted pt-2 font-size-sm">Show active users</span></h3>
                        </div>
                        <div class="card-toolbar">
                            <download-excel
                                :data   = "users"
                                :fields = "exportUsers"
                                class   = "btn btn-success mr-2"
                                name    = "Users.xls">
                                    Download Excel ({{ users.length }})
                            </download-excel>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Filter</label>
                                    <select class="form-control form-control-primary" v-model="filterRole" id="filterRole">
                                        <option value="">Filter by Role</option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Finance">Finance</option>
                                        <option value="IT Support">IT Support</option>
                                        <option value="User">User</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-checkable" id="kt_datatable">
                                <thead>
                                    <tr>
                                        <!-- <th>User ID</th> -->
                                        <th class="text-center">User</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, i) in filteredUserQueues" :key="i" >
                                       <td><small>{{user.name}}</small></td>
                                       <td><small>{{user.email}}</small></td>
                                       <td class="text-center">
                                           <a href="#" @click="setUserRole(user)">
                                                <small>{{user.user_role ? user.user_role.role : "User" }}</small>
                                           </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row col-md-12" v-if="filteredUserQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLinkUser()" class="btn btn-default btn-sm btn-fill" v-on:click="setPageUser(currentPageUser - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPageUser + 1 }} of {{ totalPagesUser }}</span>
                                <button :disabled="!showNextLinkUser()" class="btn btn-default btn-sm btn-fill" v-on:click="setPageUser(currentPageUser + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Users : {{ filteredUsers.length }} </span><br>
                            </div>
                        </div>

                        <div class="row" v-if="users.length == 0">
                            <div class="col-md-3 pt-5 pb-5">
                                <div class="spinner spinner-left spinner-primary spinner-lg">
                                    <span class="ml-15">Loading.. Please wait..</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  


            </div>  
        </div>  

    </div>  

    <div class="modal fade" id="change-user-role-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-md modal-fixed" role="document">
            <div class="modal-content">
                <div>
                    <button type="button" class="close mt-2 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> 
                <div class="modal-header">
                    <h2 class="col-12 modal-title text-center">Change User Role</h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">User Role</label> 
                                <select name="select" v-model="user.role" class="form-control">
                                    <option value="">Select Role</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="Finance">Finance</option>
                                    <option value="IT Support">IT Support</option>
                                    <option value="User">User</option>
                                </select>
                                <span class="text-danger" v-if="errors.role">{{ errors.role[0] }}</span>
                            </div>
                        </div>
                        <strong>Inventories</strong><br>
                        <div class="col-md-12">
                            
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.inventory_masterlist" id="inventory_masterlist"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Masterlist
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.inventory_transfer_location" id="inventory_transfer_location"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Transfer Location
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.inventory_receive_transfer" id="inventory_receive_transfer"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Receive Transfer
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.report_for_disposal" id="report_for_disposal"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        For Disposal
                                    </label>
                                </div>
                            </div>
                        </div>
                        <strong>Reports</strong><br>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.report_asset_handover_form" id="report_asset_handover_form"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Asset Hand Over Forms
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.report_employee_asset" id="report_employee_asset"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Employee Asset
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.report_borrowed_inventories" id="report_borrowed_inventories"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Borrowed Inventories
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.report_returned_inventories" id="report_returned_inventories"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Returned Inventories
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.report_disposed_logs" id="report_disposed_logs"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Disposed Logs
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.activity_logs" id="activity_logs"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Activity Logs
                                    </label>
                                </div>
                            </div>
                        </div>
                        <strong>Configuration</strong><br>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.rfid_registration_devices" id="rfid_registration_devices"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        RFID Registration Devices
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.users" id="users"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Users
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.settings" id="items"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Settings
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.items" id="items"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Items
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.requests_borrow" id="items"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Borrow Requests
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" v-model="user.requests_return" id="items"/>
                                    <label class="form-check-label" for="flexCheckDefault">
                                        Return Requests
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveChangeUserRole">Save</button>
                </div>
            </div>
        </div>
    </div>

</div>
</template>

<script>
    import JsonExcel from 'vue-json-excel'
    export default {
        components: {
            'downloadExcel': JsonExcel
        },
        data() {
            return {
                keywords: '',
                filterRole : 'Administrator',
                user : [],
                users : [],
                errors : [],
                currentPageUser: 0,
                itemsPerPageUser: 10, 
                exportUsers : {
                    'ID' : {
                        callback: (value) => {
                            if(value.employee){
                                return value.employee.id;
                            }else{
                                return '';
                            }
                        }
                    },
                    'Employee' : {
                        callback: (value) => {
                            if(value.employee){
                                return value.employee.first_name + ' ' + value.employee.last_name;
                            }else{
                                return '';
                            }
                        }
                    },
                }
            }
        },
        created () {
            this.getUsers();
        },
        methods: {
            saveChangeUserRole(){
                let v = this;
                Swal.fire({
                    title: 'Are you sure you want to change role for this user?',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: `Yes`,
                    denyButtonText: `No`,
                    }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData();
                        formData.append('user_id', v.user.id ? v.user.id : "");
                        formData.append('role', v.user.role ? v.user.role : "");
                        formData.append('inventory_masterlist', v.user.inventory_masterlist ? v.user.inventory_masterlist : "");
                        formData.append('inventory_transfer_location', v.user.inventory_transfer_location ? v.user.inventory_transfer_location : "");
                        formData.append('inventory_receive_transfer', v.user.inventory_receive_transfer ? v.user.inventory_receive_transfer : "");
                        formData.append('report_for_disposal', v.user.report_for_disposal ? v.user.report_for_disposal : "");
                        formData.append('report_asset_handover_form', v.user.report_asset_handover_form ? v.user.report_asset_handover_form : "");
                        formData.append('report_employee_asset', v.user.report_employee_asset ? v.user.report_employee_asset : "");
                        formData.append('report_borrowed_inventories', v.user.report_borrowed_inventories ? v.user.report_borrowed_inventories : "");
                        formData.append('report_returned_inventories', v.user.report_returned_inventories ? v.user.report_returned_inventories : "");
                        formData.append('report_disposed_logs', v.user.report_disposed_logs ? v.user.report_disposed_logs : "");
                        formData.append('rfid_registration_devices', v.user.rfid_registration_devices ? v.user.rfid_registration_devices : "");
                        formData.append('users', v.user.users ? v.user.users : "");
                        formData.append('activity_logs', v.user.activity_logs ? v.user.activity_logs : "");
                        formData.append('items', v.user.items ? v.user.items : "");
                        formData.append('settings', v.user.settings ? v.user.settings : "");
                        formData.append('requests_borrow', v.user.requests_borrow ? v.user.requests_borrow : "");
                        formData.append('requests_return', v.user.requests_return ? v.user.requests_return : "");

                        axios.post(`/save-change-user-role`, formData)
                        .then(response =>{
                            if(response.data == "saved"){
                                v.getUsers();
                                Swal.fire('User role has been changed!', '', 'success');        
                                $('#change-user-role-modal').modal('hide');
                                v.user = [];
                                
                            }else{
                                Swal.fire('Error: Cannot changed. Please try again.', '', 'error');   
                                v.employeePositionCode.position_code = "";
                            }
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        })
                    }   
                })
            },
            setUserRole(user){
                let v = this;
                v.user = user;
                v.user.role = user.user_role ? user.user_role.role : "User"; 
                v.user.inventory_masterlist = user.user_role ? user.user_role.inventory_masterlist : ""; 
                v.user.inventory_transfer_location = user.user_role ? user.user_role.inventory_transfer_location : ""; 
                v.user.inventory_receive_transfer = user.user_role ? user.user_role.inventory_receive_transfer : ""; 
                v.user.report_for_disposal = user.user_role ? user.user_role.report_for_disposal : ""; 
                v.user.report_asset_handover_form = user.user_role ? user.user_role.report_asset_handover_form : ""; 
                v.user.report_employee_asset = user.user_role ? user.user_role.report_employee_asset : ""; 
                v.user.report_borrowed_inventories = user.user_role ? user.user_role.report_borrowed_inventories : ""; 
                v.user.report_returned_inventories = user.user_role ? user.user_role.report_returned_inventories : ""; 
                v.user.report_disposed_logs = user.user_role ? user.user_role.report_disposed_logs : ""; 
                v.user.rfid_registration_devices = user.user_role ? user.user_role.rfid_registration_devices : ""; 
                v.user.users = user.user_role ? user.user_role.users : ""; 
                v.user.activity_logs = user.user_role ? user.user_role.activity_logs : ""; 
                v.user.items = user.user_role ? user.user_role.items : ""; 
                v.user.settings = user.user_role ? user.user_role.settings : ""; 
                v.user.requests_borrow = user.user_role ? user.user_role.requests_borrow : ""; 
                v.user.requests_return = user.user_role ? user.user_role.requests_return : ""; 
                $('#change-user-role-modal').modal('show');
            },
            getUsers() {
               let v = this;
               v.users = [];
               axios.get('/get-users-data')
                .then(response => { 
                    v.users = response.data;
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            setPageUser(pageNumber) {
                this.currentPageUser = pageNumber;
            },
            resetStartRowUser() {
                this.currentPageUser = 0;
            },
            showPreviousLinkUser() {
                return this.currentPageUser == 0 ? false : true;
            },
            showNextLinkUser() {
                return this.currentPageUser == (this.totalPagesUser - 1) ? false : true;
            },
        },
        computed: {
            filteredUsers(){
                let self = this;
                if(self.users){
                    return Object.values(self.users).filter(user => {
                        if(self.filterRole){
                            if(user.user_role){
                                return user.user_role.role.toLowerCase().includes(this.filterRole.toLowerCase())
                            }
                        }else{
                            return user.name.toLowerCase().includes(this.keywords.toLowerCase()) || user.email.toLowerCase().includes(this.keywords.toLowerCase())
                        }
                    });
                }else{
                    return [];
                }
               
            },
            totalPagesUser() {
                return Math.ceil(Object.values(this.filteredUsers).length / this.itemsPerPageUser)
            },
            filteredUserQueues() {
                var index = this.currentPageUser * this.itemsPerPageUser;
                var queues_array = this.filteredUsers.slice(index, index + this.itemsPerPageUser);

                if(this.currentPageUser >= this.totalPagesUser) {
                    this.currentPageUser = this.totalPagesUser - 1
                }

                if(this.currentPageUser == -1) {
                    this.currentPageUser = 0;
                }

                return queues_array;
            },
        },
    }
</script>

<style lang="scss" scoped>

</style>