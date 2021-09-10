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
                        <h2 class="text-white font-weight-bold my-2 mr-5">Categories</h2>
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
                            <button class="btn btn-success mr-2" @click="addCategory">New</button>
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
                                        <th class="text-center">CATEGORY</th>
                                        <th class="text-center">COLOR</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in filteredCategoryQueues" :key="i" >
                                       <td align="center"><small>{{item.name}}</small></td>
                                       <td align="center">
                                            <i class="fas fa-circle" :style="'color:'+item.color"></i>
                                        </td>
                                       <td align="center">
                                           <button type="button" class="btn btn-light-primary btn-icon btn-sm" @click="editCategory(item)">
                                                <i class="flaticon-edit"></i>
                                            </button>
                                       </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row col-md-12" v-if="filteredCategoryQueues.length">
                            <div class="col-6">
                                <button :disabled="!showPreviousLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage - 1)"> Previous </button>
                                    <span class="text-dark">Page {{ currentPage + 1 }} of {{ totalPages }}</span>
                                <button :disabled="!showNextLink()" class="btn btn-default btn-sm btn-fill" v-on:click="setPage(currentPage + 1)"> Next </button>
                            </div>
                            <div class="col-6 text-right">
                                <span class="mr-2">Total Categories : {{ filteredCategories.length }} </span><br>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>  
        </div>  
    </div>

    <div class="modal fade" id="category-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
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
                                <label for="role">{{ action }} Category</label> 
                                <input type="text" class="form-control" placeholder="Input here..." v-model="category.name" @keyup.enter="saveCategory">
                                <span class="text-danger" v-if="errors.name">{{ errors.name[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="role">Color</label> 
                                <input type="color" class="form-control" v-model="category.color" @keyup.enter="saveCategory">
                                <span class="text-danger" v-if="errors.color">{{ errors.color[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-md" @click="saveCategory">Save</button>
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
            category : {
                'id' : '',
                'name' : '',
                'color' : '',
            },
            action : '',
            categories : [],
            errors : [],
            currentPage: 0,
            itemsPerPage: 10, 
        }
    },
    created () {
        this.getCategories();
    },
    methods : {
        addCategory(){
            let v = this;
            v.errors = [];
            v.category.id = '';
            v.category.name = '';
            v.category.color = '';
            v.action = 'New';
            $('#category-modal').modal('show');
        },
        editCategory(category){
            let v = this;
            v.errors = [];
            v.category.id = category.id;
            v.category.name = category.name;
            v.category.color = category.color;
            v.action = 'Update';
            $('#category-modal').modal('show');
        },
        saveCategory(){
            let v = this;
            Swal.fire({
                title: 'Are you sure you want to save this type?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: `Yes`,
                denyButtonText: `No`,
                }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData();
                    var postURL = `/setting-categories-store`;
                    if(v.action == "Update"){
                        formData.append('id', v.category.id ? v.category.id : "");
                        postURL = `/setting-categories-update`;
                    }
                    formData.append('name', v.category.name ? v.category.name : "");
                    formData.append('color', v.category.color ? v.category.color : "");
                    axios.post(postURL, formData)
                    .then(response =>{
                        if(response.data.status == "success"){
                            Swal.fire('Category has been saved!', '', 'success');        
                            $('#category-modal').modal('hide');
                            v.category.id = '';
                            v.category.name = '';  
                            v.category.color = '';  
                            this.getCategories();
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
        getCategories() {
            let v = this;
            v.categories = [];
            axios.get('/setting-categories-data')
            .then(response => { 
                v.categories = response.data;
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
        filteredCategories(){
            let self = this;
            if(self.categories){
                return Object.values(self.categories).filter(item => {
                    return item.name.toLowerCase().includes(this.keywords.toLowerCase())
                });
            }else{
                return [];
            }
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredCategories).length / this.itemsPerPage)
        },
        filteredCategoryQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredCategories.slice(index, index + this.itemsPerPage);
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