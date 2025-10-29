<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <title>Transactions</title>
    <link rel="stylesheet" href="{{ asset('css/finsync.css') }}">
</head>
<body>
    <div class="dashboardcontainer">
        <div class="headerpart">
            <div class="headerleftpart">
                <i class="fa-solid fa-bars" data-bs-toggle="offcanvas" data-bs-target="#requestsoffcanvas" aria-controls="offcanvasWithBothOptions"></i>
                <img src="images/logo.png" alt="" class="logoimg">
            </div>
        </div>
        <div class="mainbody">
            <div class="dashboardrightside">
                <div class="pagetitle">
                    <span>Bank Transactions</span>
                </div>
                <div class="datefilterview" id="datefilterview">
                    <span class="filterrange">01-10-2000 - 24-04-2025</span>
                    <i class="fa-solid fa-calendar-days" id="calenderpicker" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Select Date Range"></i>
                </div>
                <div class="overviewdiv">
                    <span>All Transactions</span>
                </div>
                <div class="requestsdiv">
                    <div class="requestsdivtopbar sticky-top">
                        <div class="filtersdiv">
                            <span class="filterselection filteractive" style="padding-left: 22px;">All</span>
                            <span class="filterselection">Credit</span>
                            <span class="filterselection">Debit</span>
                        </div>
                        <div class="requestsearchdiv">
                            <div class="searchbar">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" name="requestsearch" id="requestsearch" placeholder="Search..." autocomplete="off">
                            </div>
                            <div class="refresh">
                                <i class="fa-solid fa-arrows-rotate searchicon" id="refreshicon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Refresh"></i>
                                <i class="fa-solid fa-file-export searchicon" id="exporticon" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Export"></i>
                            </div>
                        </div>
                    </div>
                    <table class="requeststable">
                        <thead>
                            <tr class="requeststableheader">
                              <th style="width: 8%; max-width: 8%; padding-left: 14px; font-size: 14px;">Date</th>
                              <th style="width: 5%; max-width: 5%; text-align: center; vertical-align: middle; font-size: 14px;">Bank Details</th>
                              <th style="width: 5%; max-width: 5%; text-align: center; vertical-align: middle; font-size: 14px;">Type</th>
                              <th style="width: 10%; max-width: 10%; text-align: center; vertical-align: middle; font-size: 14px;">Transaction Details</th>
                              <th style="width: 15%; max-width: 15%; text-align: center; vertical-align: middle; font-size: 14px;">Customer</th>
                              <th style="width: 10%; max-width: 10%; text-align: center; vertical-align: middle; font-size: 14px;">Description</th>
                              <th style="width: 20%; max-width: 20%; text-align: center; vertical-align: middle; font-size: 14px;">Narrative</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div class="paginationview">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dashboardalerthere">
    </div>
    <div class="exchangeratemodaldiv">
        <div class="modal fade" id="exchangeratemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="rexchangeratemodalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="exchangeratemodalLabel">Set Exchange Rate</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kes" class="form-label">USD to KES Exchange Rate</label>
                            <input type="text" name="kes" class="form-control" id="kes" placeholder="e.g 132.2" oninput="textInputToBeNumber(this)">
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-primary" style="background-color: #C9187D; border: 1px solid #C9187D;" data-bs-dismiss="modal">Cancel</button>
                      <button type="button" class="btn btn-success" id="ratexport" style="background-color: #5b77d4; border: 1px solid #5b77d4;">Generate</button>
                    </div>
                  </div>
                </div>
        </div>
    </div>
    <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
    <script src="{{ asset('js/finsync.js') }}"></script>
</body>
</html>