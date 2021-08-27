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
                        <h2 class="text-white font-weight-bold my-2 mr-5">Dashboard</h2>
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
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Updated</a>
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
                <!--begin::Dashboard-->
                <!--begin::Row-->
                <div class="row">
                    <div class="col-xl-4">
                        <!--begin::Stats Widget 22-->
                        <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-3.svg)">
                            <!--begin::Body-->
                            <div class="card-body">
                                <i class="icon-xl fas fa-th-large"></i>
                                <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">0</span>
                                <span class="font-weight-bold text-muted font-size-sm">Overall Total Inventory</span>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 22-->
                    </div>
                    <div class="col-xl-4">
                        <!--begin::Stats Widget 22-->
                        <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-3.svg)">
                            <!--begin::Body-->
                            <div class="card-body">
                                <i class="icon-xl fas fa-undo"></i>
                                <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">0</span>
                                <span class="font-weight-bold text-muted font-size-sm">Total Borrowed Items Today</span>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 22-->
                    </div>
                    <div class="col-xl-4">
                        <!--begin::Stats Widget 22-->
                        <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-3.svg)">
                            <!--begin::Body-->
                            <div class="card-body">
                                <i class="icon-xl fas fas fa-redo"></i>
                                <span class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block">0</span>
                                <span class="font-weight-bold text-muted font-size-sm">Total Return Items Today</span>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 22-->
                    </div>
                </div>
            </div>
         </div>



    </div>
</div>
</template>

<script>
    export default {
        
    }
</script>

<style lang="scss" scoped>

</style>