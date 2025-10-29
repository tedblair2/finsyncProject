const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

const alertDiv=document.getElementsByClassName("dashboardalerthere")[0]
const refreshIcon=document.getElementById("refreshicon")

const requestsbody=document.querySelector('.requeststable tbody')
const paginationView=document.getElementsByClassName("pagination")[0]
const transactionSearch=document.getElementById("requestsearch")
const daterangefilterviewspan=document.querySelector('#datefilterview span')
const exportIconBtn=document.getElementById("exporticon")

const allSelection=document.getElementsByClassName("filterselection")[0]
const creditSelection=document.getElementsByClassName("filterselection")[1]
const debitSelection=document.getElementsByClassName("filterselection")[2]

const exchangeRateModal=new bootstrap.Modal('#exchangeratemodal', {
    keyboard: false
})
const ratexportBtn=document.getElementById("ratexport")
const kesInput=document.getElementById("kes")

var page=1
var perPage=30
var pages=1
var query=''
var filter='all'

const $picker = $('#calenderpicker');
var rangeStart = moment().startOf('year');
var rangeEnd = moment();


const apiClient=axios.create({
    headers:{
        "Accept":"application/json",
        "X-Requested-With": "XMLHttpRequest"
    },
    withCredentials: true,
    withXSRFToken: true
})

window.addEventListener('pageshow',function(){
    var urlString=window.location.href
    var url=new URL(urlString)
    var pageParam=url.searchParams.get('page')
    var filterParam=url.searchParams.get('filter')
    var fromParam=url.searchParams.get('from')
    var toParam=url.searchParams.get('to')
    page=pageParam ? parseInt(pageParam) : 1
    filter=filterParam ? filterParam : 'all'

    rangeStart=fromParam ? moment(fromParam) : moment().startOf('year')
    rangeEnd=toParam ? moment(toParam) : moment()

    $picker.daterangepicker({
        startDate: rangeStart,
        endDate: rangeEnd,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    },cb);

    $picker.on('show.daterangepicker', function (ev, picker) {
        picker.ranges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };
    });

    cb(rangeStart, rangeEnd);
})

function cb(start, end) {
    rangeStart = start;
    rangeEnd = end;
    daterangefilterviewspan.textContent = start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY');
    getTransactions()
}

allSelection.addEventListener('click',(e)=>{
    filter='all'
    const activeFilter=document.getElementsByClassName("filteractive")[0]
    activeFilter.classList.remove("filteractive")
    e.target.classList.add("filteractive")
    getTransactions()
})

creditSelection.addEventListener('click',(e)=>{
    filter='credit'
    const activeFilter=document.getElementsByClassName("filteractive")[0]
    activeFilter.classList.remove("filteractive")
    e.target.classList.add("filteractive")
    getTransactions()
})

debitSelection.addEventListener('click',(e)=>{
    filter='debit'
    const activeFilter=document.getElementsByClassName("filteractive")[0]
    activeFilter.classList.remove("filteractive")
    e.target.classList.add("filteractive")
    getTransactions()
})

transactionSearch.addEventListener('input',(e)=>{
    query=e.target.value
})

transactionSearch.addEventListener('keyup',(e)=>{
    if (e.key === 'Enter') {
        getTransactions()
    }
})

refreshIcon.addEventListener('click',()=>{
    getTransactions()
})

exportIconBtn.addEventListener('click',()=>{
    exchangeRateModal.show()
})

ratexportBtn.addEventListener('click',()=>{
    const rate=kesInput.value.trim()
    if(rate.length===0){
        appendAlert("Please enter exchange rate","warning")
        return
    }
    if(isNaN(rate) || parseFloat(rate)<=0){
        appendAlert("Please enter valid exchange rate","warning")
        return
    }
    exportTransaction(parseFloat(rate))
    exchangeRateModal.hide()
})

const exportTransaction=async(kesRate)=>{
    showLoading(true)
    try{
        const response=await apiClient.get(`/api/transactions/export?query=${query}&filter=${filter}&start=${rangeStart.format('YYYY-MM-DD HH:mm:ss')}&end=${rangeEnd.format('YYYY-MM-DD HH:mm:ss')}&usdToKesRate=${kesRate}`,{
            responseType: 'blob'
        })
        const filename=response.headers['x-filename'] || "transactions.csv"
        const blob = new Blob([response.data]);
        const href = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = href;
        link.download=filename
        document.body.appendChild(link);
        link.click()

        document.body.removeChild(link);
        URL.revokeObjectURL(href)
    }catch(error){
        console.log(error)
        if (error.response) {
            console.log(error)
            // Server responded with a status code outside the 2xx range
            if (error.response.status === 401) {
                navigateToLogin()
            } else if (error.response.status === 419) {
                appendAlert("Session expired or CSRF token issue. Please refresh the page.","danger");
            } else {
                appendAlert(`Error: ${error.response.data.message || "Something went wrong"}`,"danger");
            }
        } else if (error.request) {
            // Request made but no response received
            appendAlert("No response from server. Please check your internet connection.","danger");
        } else {
            // Other errors
            appendAlert("An error occurred. Please try again.","danger");
        }
    }
    showLoading(false)
}

function textInputToBeNumber(input){
    input.value = input.value.replace(/[^0-9.]/g, '')     // remove non-numeric and non-dot
        .replace(/(\..*)\./g, '$1'); // allow only one dot
}

const getTransactions=async()=>{
    showLoading(true)
    try{
        const response=await apiClient.get(`/api/transactions?query=${query}&perPage=${perPage}&page=${page}&filter=${filter}&start=${rangeStart.format('YYYY-MM-DD HH:mm:ss')}&end=${rangeEnd.format('YYYY-MM-DD HH:mm:ss')}`,{
            headers:{
                'Content-Type':'application/json'
            }
        })
        requestsbody.innerHTML=''
        if(response.data.isSuccess===true){
            console.log(response.data)
            pages=response.data.pages
            response.data.data.forEach(transaction=>{
                const [datePart, timePart] = transaction.transaction_date.split(" ");
                const dateStr=getFormattedDateStr(datePart)
                const timeStr=getFormattedTimeStr(timePart)
                requestsbody.innerHTML += `<tr class="requeststablebody">
                                <td style="padding-left: 14px;">
                                    <div class="msgdatediv">
                                        <span class="msgdate" style="font-size: 13px;">${dateStr}</span>
                                        <span class="msgtime" style="font-size: 13px;">${timeStr}</span>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <div class="msgdatediv">
                                        <span class="msgdate" style="font-size: 14px;">${transaction.bank_name}</span>
                                        <span class="msgtime" style="font-size: 14px;">${transaction.account_number}</span>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle; font-size: 14px;">${transaction.creditdebitflag}</td>
                                <td style="text-align: center; vertical-align: middle; font-size: 14px;">
                                    <div class="receiverdiv">
                                        <span class="receivername" style="font-size: 14px;">${transaction.transaction_id}</span>
                                        <span class="receiverphone" style="font-size: 14px;">${transaction.amount}</span>
                                    </div>
                                </td>
                                <td style="text-align: center; vertical-align: middle; word-break: break-word; white-space: normal; overflow-wrap: anywhere; font-size: 13px;">${transaction.customer_name}</td>
                                <td style="text-align: center; vertical-align: middle; word-break: break-word; white-space: normal; overflow-wrap: anywhere; font-size: 14px;">${transaction.transaction_description}</td>
                                <td style="text-align: center; vertical-align: middle; word-break: break-word; white-space: normal; overflow-wrap: anywhere; font-size: 13px;">${transaction.narrative}</td>
                            </tr>`
            })
            renderPagination(page)
        }
    }catch(error){
        console.log(error)
        if (error.response) {
            console.log(error)
            // Server responded with a status code outside the 2xx range
            if (error.response.status === 401) {
                navigateToLogin()
            } else if (error.response.status === 419) {
                appendAlert("Session expired or CSRF token issue. Please refresh the page.","danger");
            } else {
                appendAlert(`Error: ${error.response.data.message || "Something went wrong"}`,"danger");
            }
        } else if (error.request) {
            // Request made but no response received
            appendAlert("No response from server. Please check your internet connection.","danger");
        } else {
            // Other errors
            appendAlert("An error occurred. Please try again.","danger");
        }
    }
    showLoading(false)
}

const renderPagination=(pageinput)=>{
    paginationView.innerHTML=''
    var paginationhtml=''
    var selectedpage=Math.min(pageinput, pages)
    var start=Math.max(pageinput-2,1)
    var end=Math.min(start+4,pages)
    start = Math.max(end - 4, 1);

    const prevDisabled=selectedpage===1 ? "disabled" : ""
    const nextDisabled=selectedpage===pages ? "disabled" : ""
    const prevColor=selectedpage===1 ? "rgb(83, 84, 85)" : "#5b77d4"
    const nextColor=selectedpage===pages ? "rgb(83, 84, 85)" : "#5b77d4"

    paginationhtml +=`<li class="page-item ${prevDisabled}">
                            <a class="page-link" href="?page=${page-1}&filter=${filter}&from=${rangeStart.format('YYYY-MM-DD HH:mm:ss')}&to=${rangeEnd.format('YYYY-MM-DD HH:mm:ss')}" data-page="${page - 1}" style="color: ${prevColor};">Prev</a>
                          </li>`

    for(i=start;i<=end;i++){
        var lihtml=''
        if(i===parseInt(selectedpage)){
            lihtml +=`<li class="page-item active" aria-current="page" style="background-color: #5b77d4;"><a class="page-link" href="#" style="background-color: #5b77d4; border: 1px solid #5b77d4; outline: none; color: white;" data-page="${i}" >${i}</a></li>`
        }else{
            lihtml += `<li class="page-item"><a class="page-link" href="?page=${i}&filter=${filter}&from=${rangeStart.format('YYYY-MM-DD HH:mm:ss')}&to=${rangeEnd.format('YYYY-MM-DD HH:mm:ss')}" data-page="${i}" style="color: #5b77d4;">${i}</a></li>`
        }
        paginationhtml += lihtml
    }

    paginationhtml +=`<li class="page-item ${nextDisabled}">
                            <a class="page-link" href="?page=${page+1}&filter=${filter}&from=${rangeStart.format('YYYY-MM-DD HH:mm:ss')}&to=${rangeEnd.format('YYYY-MM-DD HH:mm:ss')}" data-page="${page + 1}" style="color: ${nextColor};">Next</a>
                          </li>`
    
    if(pages>1){
        paginationView.innerHTML=paginationhtml
    }
}

const getFormattedDateStr=(datestr)=>{
    const [year, month, day] = datestr.split("-");
    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const formattedDate = `${day} ${months[parseInt(month, 10) - 1]} ${year}`;
    return formattedDate
}

const getFormattedTimeStr=(timestr)=>{
    let [hour, minute] = timestr.split(":");
    const isPM = hour >= 12;
    hour = hour % 12 || 12;
    const formattedTime = `${hour}:${minute} ${isPM ? "PM" : "AM"}`;
    return formattedTime
}

const showLoading=(isLoading)=>{
    if(isLoading===true){
        refreshIcon.classList.add("fa-spin")
    }else{
        refreshIcon.classList.remove("fa-spin")
    }
}

const appendAlert=(message,type)=>{
    alertDiv.innerHTML=`<div class="alert alert-${type}" role="alert">
                ${message}
            </div>`
    setTimeout(() => {
        alertDiv.innerHTML = '';
    }, 2500);
}