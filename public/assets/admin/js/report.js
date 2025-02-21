document.addEventListener("DOMContentLoaded", function(event) {
    var date = new Date();
    var dateFrom = date.getFullYear()+"-"+(date.getMonth()+1)+"-01";
    var dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
    var range= "ThisMonth";

    var _customerChart = null;
    var _orderChart = null;
    var _saleChart = null;
    var _earningChart = null; 

    var topSellingTable = document.getElementById('top-selling-table');

    var totalRevenue = document.getElementById('total-revenue');
    var totalProduct = document.getElementById('total-product');
    var totalOrder = document.getElementById('total-order');
    var totalCustomer = document.getElementById('total-customer');

    var rangeDropdownBtn = document.getElementById('range-dropdown-btn');
    var rangeDropdownMenu = document.getElementById('range-dropdown-menu');

    var dateRange = document.querySelectorAll('#rangeDate');
    dateRange.forEach((it) => {
        it.onclick = () => {
            switch(it.dataset.value){
            case 'Today':
                dateFrom = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                range = "Today";
                getReport();
                getTopSelling();
                break; 
            case 'ThisWeek':
                dateFrom = getPriorDate(date.getDay());
                dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                range = "ThisWeek";
                getReport();
                getTopSelling();
                break;
            case 'Last7Days': 
                dateFrom = getPriorDate(7);
                dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                range = "Last7Days";
                getReport();
                getTopSelling();
                break;
            case 'Last30Days':
                dateFrom = getPriorDate(30);
                dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                range = "Last30Days";
                getReport();
                getTopSelling();
                break;
            case 'ThisMonth':
                dateFrom = date.getFullYear()+"-"+(date.getMonth()+1)+"-01";
                dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                range = "ThisMonth";
                getReport();
                getTopSelling();
                break;
            case 'ThisYear':
                dateFrom = date.getFullYear()+"-01-01";
                dateTo = date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
                range = "ThisYear";
                getReport();
                getTopSelling();
                break;
            case 'Custom':
                dateFrom = document.querySelector('input[name="from"]').value.replace('/','-');
                dateTo = document.querySelector('input[name="to"]').value.replace('/','-');;
                range = "Custom";
                getReport();
                getTopSelling();
                break;
            default:
                break;
            }

            rangeDropdownMenu.classList.toggle('show');
            rangeDropdownBtn.innerHTML = "From "+dateFrom+" to "+dateTo;
        }
    });

    /**
    FETCH DATA
    */
    getReport();
    getTopSelling();

    /**
     * Fech report data from web server for chart display
    */
    async function getReport(){
        const queryParams = {
            from_date: dateFrom,
            to_date: dateTo,
            range: range,
        }

        const queryString = new URLSearchParams(queryParams).toString();

        const url = "/admin/report/data"+"?"+queryString;

        try{
            const response = await fetch(url,{
                method: "GET",
                mode: "cors",
                cache: "no-cache",
                credentials: "same-origin",
            });
            if (response.ok) {
                const result = await response.json();

                setCount(result.data);
                customerChart(result.data);
                orderChart(result.data);
                saleChart(result.data);
                earningChart(result.data);
            }else{
                console.log(await response.text());
            }
        }catch(error){
            console.log(error);
        }
    }

    async function getTopSelling(){
         const queryParams = {
            from_date: dateFrom,
            to_date: dateTo,
        }

        const queryString = new URLSearchParams(queryParams).toString();

        const url = "/admin/report/top-selling"+"?"+queryString;

        try{
            const response = await fetch(url,{
                method: "GET",
                mode: "cors",
                cache: "no-cache",
                credentials: "same-origin",
            });
            if (response.ok) {
                const result = await response.json();

                setTopSelling(result.data);
            }else{
                console.log(await response.text());
            }
        }catch(error){
            console.log(error);
        }
    } 

    function setTopSelling(data){
        var rows = "";

        if (data.products.length) {
            data.products.forEach((it) =>{
                rows += `<tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center">
                                 <img class="rounded-2 me-4" src="/assets/img/products/`+it.image+`" width="40" height="40">
                                <a href="/admin/product/edit/`+it.id+`"><h6 class="m-0">`+it.name+`</h6></a>
                            </div>
                        </td>
                        <td><span>`+data.currency+it.price+`</span></td>
                        <td><span>`+it.quantity+`</span></td>
                        <td><span>`+it.sold+`</span></td>
                    </tr>`;
            });

            topSellingTable.querySelector('tbody').innerHTML = rows;
        }else{

        }
    }

    function setCount(data){
        var totalRev = "";
    
        if (data.totalRevenue > 1000000000) {
            totalRev = data.currency+((data.totalRevenue/1000000000).toFixed(2))+"B";
        }else if(data.totalRevenue > 1000000){
            totalRev = data.currency+((data.totalRevenue/1000000).toFixed(2))+"M";
        }else if(data.totalRevenue > 1000){
            totalRev = data.currency+((data.totalRevenue/1000).toFixed(2))+"K";
        }else{
            totalRev = data.currency+""+data.totalRevenue;
        }

        totalRevenue.innerHTML = totalRev;
        totalProduct.innerHTML = data.totalProduct;
        totalOrder.innerHTML = data.totalOrder;
        totalCustomer.innerHTML = data.totalCustomer;
    }

    function customerChart(data){
        var xValues = [];
        var yValues = [];

        if (data.customerData.length) {
            data.customerData.forEach((it) => {
                yValues.push(it.Total);
                xValues.push(it.Month);
            });
        }

        if (_customerChart != null) {
            _customerChart.destroy();
        }

       _customerChart = new Chart(document.getElementById('customerChart'),{
            type: "line",
            data: { 
                labels: xValues,
                datasets: [{
                    lineTension: 0.5,
                    label:"Number of customers",
                    backgroundColor:"rgba(0,0,255,0.1)",
                    borderColor: "rgba(0,0,255,0.5)",
                    data: yValues
                }]
            },
            options:{}
        });
    }

    function orderChart(data){
        const xValues = [];
        const yValues = [];

        if (data.orderData.length) {
            data.orderData.forEach((it) => {
                yValues.push(it.Total);
                xValues.push(it.Month);
            });
        }

        if (_orderChart != null) {
            _orderChart.destroy();
        }

        _orderChart = new Chart(document.getElementById('orderChart'),{
            type: "line",
            data: { 
                labels: xValues,
                datasets: [{
                    label:"Number of orders",
                    backgroundColor:"rgba(0,0,255,0.1)",
                    borderColor: "rgba(0,0,255,0.5)",
                    data: yValues
                }]
            },
            options:{}
        });
    }

    function saleChart(data){
        const xValues = [];
        const yValues = [];

        if (data.saleData.length) {
            data.saleData.forEach((it) => {
                yValues.push(it.Total);
                xValues.push(it.Month);
            });
        }

        if (_saleChart != null) {
            _saleChart.destroy();
        }

        _saleChart = new Chart(document.getElementById('saleChart'),{
            type: "line",
            data: { 
                labels: xValues,
                datasets: [{
                    fill: true,
                    showLine: true,
                    label: data.currency,
                    backgroundColor:"rgba(255,204,0,0.1)",
                    borderColor: "rgba(255,204,0,0.5)",
                    data: yValues
                }]
            },
            options:{}
        });
    }

    function earningChart(data){
        var xValues = [];
        var yValues = [];

        xValues = ["Completed", "Pending"];
        yValues = [data.earningCompleted, data.earningPending];

        if (_earningChart != null) {
            _earningChart.destroy();
        }
    
        _earningChart = new Chart(document.getElementById('earningChart'),{
            type: "doughnut",
            data: { 
                labels: xValues,
                datasets: [{
                    backgroundColor:["rgba(0,153,1,0.5)","rgba(204,0,0,0.5)"],
                    data: yValues
                }]
            },
            options:{}
        });
    }

    /**
    * Return previous date of given number of days
    * @param Int days for number of days default 0
    * @return String of date format "YYYY-MM-DD"
     * */
    function getPriorDate(days = 0){
        var d = new Date(
            new Date().setDate(
                new Date().getDate() - days
            )
        );

        return  d.getFullYear()+"-"+(d.getUTCMonth()+1)+"-"+d.getUTCDate();
    }
});