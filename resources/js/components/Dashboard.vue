<template>
<div>
   <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
            <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap inventories-container">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex flex-column">
                        <h2 class="text-white font-weight-bold my-2 mr-5">Dashboard</h2>
                        <div class="d-flex align-items-center font-weight-bold my-2">
                            <!-- <a href="#" class="opacity-75 hover-opacity-100">
                                <i class="flaticon2-shelter text-white icon-1x"></i>
                            </a> -->
                            <!-- <span class="label label-dot label-sm bg-white opacity-75 mx-3"></span>
                            <a href="" class="text-white text-hover-white opacity-75 hover-opacity-100">Asset Transfer</a> -->
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="#" @click="dashboardData" class="btn btn-transparent-white font-weight-bold py-3 px-6 mr-2">Refresh</a>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-fluid">
            <div class="container inventories-container">

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-xl-4">
                                <a href="#" class="card card-custom bg-success bg-hover-state-success bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-3.svg)">
                                    <div class="card-body">
                                        <i class="icon-xl fas fa-th-large text-inverse-success"></i>
                                        <span class="text-inverse-success card-title font-weight-bolder font-size-h2 mb-0 mt-6 d-block">{{overall_total_inventory.length}}</span>
                                        <span class="text-inverse-success font-weight-bold font-size-sm">Overall Total Inventory</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4">
                                <a href="/reports-borrow-logs" class="card card-custom bg-primary bg-hover-state-primary bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-3.svg)">
                                    <div class="card-body">
                                        <i class="icon-xl fas fa-undo text-inverse-primary"></i>
                                        <span class="text-inverse-primary card-title font-weight-bolder font-size-h2 mb-0 mt-6 d-block">{{total_borrowed_items_today.length}}</span>
                                        <span class="text-inverse-primary font-weight-bold font-size-sm">Total Borrowed Items Today</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-4">
                                <a href="/reports-return-logs" class="card card-custom bg-warning bg-hover-state-warning bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-3.svg)">
                                    <div class="card-body">
                                        <i class="icon-xl fas fas fa-redo text-inverse-warning"></i>
                                        <span class="text-inverse-warning card-title font-weight-bolder font-size-h2 mb-0 mt-6 d-block">{{total_returned_items_today.length}}</span>
                                        <span class="text-inverse-warning font-weight-bold font-size-sm">Total Return Items Today</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">Inventory Per Location</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <polar-chart :chart-data="location_chart" :height="200"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card card-custom gutter-b">
                                    <div class="card-header">
                                        <div class="card-title">
                                            <h3 class="card-label">Inventory Per Type</h3>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <polar-chart :chart-data="type_chart" :height="200"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>
</template>

<script>
    import BarChart from './Charts/BarChart.js'
    import PolarChart from './Charts/PolarChart.js'
    export default {
         components: {
            BarChart,PolarChart
        },
        data() {
            return {
                overall_total_inventory: '',
                total_borrowed_items_today: '',
                total_returned_items_today: '',
                location_chart: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Location',
                            backgroundColor: [],
                            pointBackgroundColor: 'white',
                            borderWidth: 1,
                            pointBorderColor: 'rgb(0,84,206)',
                            data: []
                        },
                    ]
                },
                type_chart: {
                    labels: [],
                    datasets: [
                        {
                            // label: 'Type',
                            backgroundColor: [],
                            pointBackgroundColor: 'white',
                            borderWidth: 1,
                            // pointBorderColor: 'rgb(1,156,140)',
                            data: []
                        },
                    ]
                },
                inventoriesActiveData : [],
                inventoriesSpareData : [],
                inventoriesLoanItemsData : [],
                inventoriesMaintenanceData : [],

            }
        },
        created () {
            this.dashboardData();
            this.getInventoriesActiveData();
            this.getInventoriesSpareData();
            this.getInventoriesLoanItemsData();
            this.getInventoriesMaintenanceData();
        },
        methods: {
            getInventoriesActiveData(){
                this.inventoriesActiveData = [];
                axios.get('/inventories-active-data')
                .then(response => { 
                    this.inventoriesActiveData = response.data;
                })
            },
            getInventoriesSpareData(){
                this.inventoriesSpareData = [];
                axios.get('/inventories-spare-data')
                .then(response => { 
                    this.inventoriesSpareData = response.data;
                })
            },
            getInventoriesLoanItemsData(){
                this.inventoriesLoanItemsData = [];
                axios.get('/inventories-loan-items-data')
                .then(response => { 
                    this.inventoriesLoanItemsData = response.data;
                })
            },
            getInventoriesMaintenanceData(){
                this.inventoriesMaintenanceData = [];
                axios.get('/inventories-maintenance-data')
                .then(response => { 
                    this.inventoriesMaintenanceData = response.data;
                })
            },
            dashboardData() {
                let v = this;
                v.overall_total_inventory = '';
                v.total_borrowed_items_today = '';
                v.total_returned_items_today = '';
                v.per_location = [];
                axios.get('/dashboard-data')
                .then(response => { 
                    v.overall_total_inventory = response.data.overall_total_inventory;
                    v.total_borrowed_items_today = response.data.total_borrowed_items_today;
                    v.total_returned_items_today = response.data.total_returned_items_today;
                    if(response.data.per_location){
                        v.perLocationData(response.data.per_location);
                        v.perTypeData(response.data.per_type);
                    }
                    
                })
                .catch(error => { 
                    v.errors = error.response.data.error;
                })
            },
            perLocationData(per_location){
                let v = this;
                var counts = [];
                var names = [];
                var bg_colors = [];
                
                per_location.forEach(entry => {
                    names.push(entry.name + ' (' + entry.count + ')');
                    counts.push(entry.count);
                    bg_colors.push(entry.color);

                });
               

                v.location_chart = {
                    labels: names,
                    datasets: [
                        {
                            label: 'Location',
                            backgroundColor: bg_colors,
                            pointBackgroundColor: 'white',
                            borderWidth: 1,
                            pointBorderColor: 'rgb(0,84,206)',
                            data: counts
                        },
                    ]
                };


            },
            perTypeData(per_type){
                let v = this;
                var counts = [];
                var names = [];
                var bg_colors = [];
                per_type.forEach(entry => {
                    names.push(entry.name + ' (' + entry.count + ')');
                    counts.push(entry.count);
                    bg_colors.push(entry.color);

                });
                v.type_chart = {
                    labels: names,
                    datasets: [
                        {
                            label: 'Type',
                            backgroundColor: bg_colors,
                            pointBackgroundColor: 'white',
                            borderWidth: 1,
                            pointBorderColor: 'rgb(0,84,206)',
                            data: counts
                        },
                    ]
                };


            },
        },
        
    }
</script>

<style lang="scss" scoped>
     @media (min-width: 1400px){
        .inventories-container{
            max-width: 1840px!important;
        }
    }
</style>