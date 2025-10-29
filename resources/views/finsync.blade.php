<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Transactions</title>
    <link rel="stylesheet" href="{{ asset('css/finsync.css') }}">
    <link rel="stylesheet" href="{{ asset('favicon.png') }}">
    <style>
        .headerrightpart {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-right: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 8px;
            background: #273c88;
            color: white;
            transition: background-color 0.3s;
        }

        .user-profile:hover {
            background-color: #c8187d;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            line-height: 1.2;
        }

        .user-role {
            font-size: 12px;
            color: #666;
            line-height: 1.2;
        }

        .dropdown-toggle::after {
            margin-left: 8px;
        }

        .login-modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .login-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }

        .user-select-option {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-select-option:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
            transform: translateY(-2px);
        }

        .user-select-option.selected {
            border-color: #667eea;
            background-color: #f0f3ff;
        }

        .user-select-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }

        .user-select-info {
            flex: 1;
        }

        .user-select-name {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .user-select-role {
            font-size: 13px;
            color: #666;
        }

        .user-select-check {
            font-size: 20px;
            color: #667eea;
            display: none;
        }

        .user-select-option.selected .user-select-check {
            display: block;
        }

        @media (max-width: 768px) {
            .user-info {
                display: none;
            }
        }

        /* Main Body Styling */
        .mainbody {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: calc(100vh - 70px);
            padding: 30px;
        }

        .dashboardrightside {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            padding: 30px;
            max-width: 1700px;
            margin: 0 auto;
        }

        /* Page Title */
        .pagetitle {
            margin-bottom: 25px;
        }

        .pagetitle span {
            font-size: 32px;
            font-weight: 700;
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Date Filter */
        .datefilterview {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: #273c88;
            padding: 12px 20px;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            /* box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); */
            margin-bottom: 25px;
        }

        .datefilterview:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .filterrange {
            font-weight: 600;
            font-size: 14px;
        }

        #calenderpicker {
            font-size: 18px;
            cursor: pointer;
        }

        /* Overview Section */
        .overviewdiv {
            background: #c8187d;
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.2);
        }

        .overviewdiv span {
            color: white;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Requests Container */
        .requestsdiv {
            background: #fafbfc;
            border-radius: 15px;
            padding: 20px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        /* Top Bar */
        .requestsdivtopbar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filtersdiv {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filterselection {
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 14px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
        }

        .filterselection:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
        }

        .filterselection.filteractive {
            background: #273c88;
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Search Section */
        .requestsearchdiv {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .searchbar {
            flex: 1;
            min-width: 250px;
            position: relative;
            display: flex;
            align-items: center;
        }

        .searchbar i {
            position: absolute;
            left: 15px;
            color: #999;
            font-size: 16px;
        }

        .searchbar input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.3s;
            outline: none;
        }

        .searchbar input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .refresh {
            display: flex;
            gap: 10px;
        }

        .searchicon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            color: #666;
        }

        .searchicon:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            transform: rotate(180deg);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        #exporticon:hover {
            transform: translateY(-2px) rotate(0deg);
        }

        /* Table Styling */
        .requeststable {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .requeststableheader {
            background: #273c88;
            color: white;
        }

        .requeststableheader th {
            padding: 18px 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 13px !important;
        }

        .requeststable tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        .requeststable tbody tr:hover {
            background: #f8f9ff;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .requeststable tbody td {
            padding: 16px 14px;
            color: #333;
            font-size: 14px;
        }

        /* Pagination */
        .paginationview {
            margin-top: 25px;
            padding: 20px;
            background: white;
            border-radius: 12px;
        }

        .pagination {
            margin: 0;
        }

        .page-link {
            border: 2px solid #e0e0e0;
            color: #667eea;
            margin: 0 4px;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .page-link:hover {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: rgb(39, 60, 136);
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mainbody {
                padding: 15px;
            }

            .dashboardrightside {
                padding: 20px;
                border-radius: 15px;
            }

            .pagetitle span {
                font-size: 24px;
            }

            .requestsdivtopbar {
                padding: 15px;
            }

            .filtersdiv {
                justify-content: center;
            }

            .requestsearchdiv {
                flex-direction: column;
            }

            .searchbar {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="dashboardcontainer">
        <div class="headerpart">
            <div class="headerleftpart">
                <i class="fa-solid fa-bars" data-bs-toggle="offcanvas" data-bs-target="#requestsoffcanvas"
                    aria-controls="offcanvasWithBothOptions"></i>
                <img src="images/logo.png" alt="" class="logoimg">
            </div>
            <div class="headerrightpart">
                <div class="dropdown">
                    <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}<div class="user-avatar" id="userAvatar"></div>

                        <i class="fa-solid fa-chevron-down" style="font-size: 12px; color: #666;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}"><i
                                    class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mainbody">
            <div class="dashboardrightside">
                <div class="pagetitle">
                    <span>Bank Transactions</span>
                </div>
                <div class="datefilterview" id="datefilterview">
                    <span class="filterrange" style="color: white"></span>
                    <i class="fa-solid fa-calendar-days" id="calenderpicker" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="Select Date Range"></i>
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
                                <input type="text" name="requestsearch" id="requestsearch" placeholder="Search..."
                                    autocomplete="off">
                            </div>
                            <div class="refresh">
                                <i class="fa-solid fa-arrows-rotate searchicon" id="refreshicon"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Refresh"></i>
                                <i class="fa-solid fa-file-export searchicon" id="exporticon" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="Export"></i>
                            </div>
                        </div>
                    </div>
                    <table class="requeststable">
                        <thead>
                            <tr class="requeststableheader" style="background: #273c88; color:white">
                                <th style="width: 8%; max-width: 8%; padding-left: 14px; font-size: 14px;">Date</th>
                                <th
                                    style="width: 5%; max-width: 5%; text-align: center; vertical-align: middle; font-size: 14px;">
                                    Bank Details</th>
                                <th
                                    style="width: 5%; max-width: 5%; text-align: center; vertical-align: middle; font-size: 14px;">
                                    Type</th>
                                <th
                                    style="width: 10%; max-width: 10%; text-align: center; vertical-align: middle; font-size: 14px;">
                                    Transaction Details</th>
                                <th
                                    style="width: 15%; max-width: 15%; text-align: center; vertical-align: middle; font-size: 14px;">
                                    Customer</th>
                                <th
                                    style="width: 10%; max-width: 10%; text-align: center; vertical-align: middle; font-size: 14px;">
                                    Description</th>
                                <th
                                    style="width: 20%; max-width: 20%; text-align: center; vertical-align: middle; font-size: 14px;">
                                    Narrative</th>
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

    <!-- User Selection Modal -->


    <div class="exchangeratemodaldiv">
        <div class="modal fade" id="exchangeratemodal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="rexchangeratemodalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exchangeratemodalLabel">Set Exchange Rate</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kes" class="form-label">USD to KES Exchange Rate</label>
                            <input type="text" name="kes" class="form-control" id="kes"
                                placeholder="e.g 132.2" oninput="textInputToBeNumber(this)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            style="background-color: #C9187D; border: 1px solid #C9187D;"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="ratexport"
                            style="background-color: #5b77d4; border: 1px solid #5b77d4;">Generate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/axios@1.6.7/dist/axios.min.js"></script>
    <script src="{{ asset('js/finsync.js') }}"></script>
    <script>
        // Available users list - you can fetch this from your backend
        const availableUsers = [{
                id: 1,
                name: "John Doe",
                role: "Administrator",
                initials: "JD"
            },
            {
                id: 2,
                name: "Jane Smith",
                role: "Manager",
                initials: "JS"
            },
            {
                id: 3,
                name: "Mike Johnson",
                role: "Accountant",
                initials: "MJ"
            },
            {
                id: 4,
                name: "Sarah Williams",
                role: "Analyst",
                initials: "SW"
            },
            {
                id: 5,
                name: "David Brown",
                role: "Supervisor",
                initials: "DB"
            }
        ];

        let selectedUserId = null;
        let userSelectModalInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modal
            const userSelectModalEl = document.getElementById('userSelectModal');
            userSelectModalInstance = new bootstrap.Modal(userSelectModalEl);

            // Show user selection modal on page load if no user is logged in
            const currentUser = sessionStorage.getItem('currentUser');
            if (!currentUser) {
                populateUserList();
                userSelectModalInstance.show();
            } else {
                const user = JSON.parse(currentUser);
                updateUserDisplay(user);
            }

            // Change user button
            document.getElementById('changeUserBtn').addEventListener('click', function(e) {
                e.preventDefault();
                populateUserList();
                userSelectModalInstance.show();
            });

            // Logout button
            document.getElementById('logoutBtn').addEventListener('click', function(e) {
                e.preventDefault();
                sessionStorage.removeItem('currentUser');
                location.reload();
            });

            // Confirm login button
            document.getElementById('confirmLoginBtn').addEventListener('click', function() {
                if (selectedUserId) {
                    const user = availableUsers.find(u => u.id === selectedUserId);
                    sessionStorage.setItem('currentUser', JSON.stringify(user));
                    updateUserDisplay(user);
                    userSelectModalInstance.hide();
                } else {
                    alert('Please select a user');
                }
            });
        });

        function populateUserList() {
            const userList = document.getElementById('userList');
            userList.innerHTML = '';

            availableUsers.forEach(user => {
                const userOption = document.createElement('div');
                userOption.className = 'user-select-option';
                userOption.dataset.userId = user.id;
                userOption.innerHTML = `
                    <div class="user-select-avatar">${user.initials}</div>
                    <div class="user-select-info">
                        <div class="user-select-name">${user.name}</div>
                        <div class="user-select-role">${user.role}</div>
                    </div>
                    <i class="fa-solid fa-check-circle user-select-check"></i>
                `;

                userOption.addEventListener('click', function() {
                    document.querySelectorAll('.user-select-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    selectedUserId = parseInt(this.dataset.userId);
                });

                userList.appendChild(userOption);
            });
        }

        function updateUserDisplay(user) {
            document.getElementById('userName').textContent = user.name;
            document.getElementById('userRole').textContent = user.role;
            document.getElementById('userAvatar').textContent = user.initials;
        }
    </script>
</body>

</html>
