<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Orders - Luxury POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; margin: 0; }
        .container { max-width: 1400px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(44,62,80,0.07); padding: 30px; }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        .filter-bar {
            display: flex;
            gap: 18px;
            align-items: center;
            margin-bottom: 18px;
            background: #f8f9fa;
            padding: 12px 18px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(44,62,80,0.07);
        }
        .filter-bar label {
            font-weight: 600;
            color: #2c3e50;
            margin-right: 6px;
        }
        .filter-bar select, .filter-bar input[type="date"] {
            padding: 7px 12px;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            font-size: 1rem;
            background: #fff;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: left; }
        th { background: #2c3e50; color: #fff; }
        .actions a { margin-right: 10px; color: #007bff; text-decoration: none; cursor: pointer; }
        .actions a:hover { text-decoration: underline; }
        .back-btn { background: #b38b6d; color: #fff; padding: 8px 18px; border-radius: 4px; text-decoration: none; font-weight: 600; }
        .back-btn:hover { background: #8c6a4a; }
        .loading, .error { text-align: center; padding: 20px; }
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000;
        }
        .modal-content {
            background: #fff; border-radius: 8px; padding: 30px; max-width: 800px; width: 95vw; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative; max-height: 90vh; overflow-y: auto;
        }
        .modal-close {
            position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 1.5rem; color: #333; cursor: pointer;
        }
        .modal-content h3 { margin-top: 0; }
        .modal-content table { margin-top: 10px; }

        #refresh-btn.refreshing i {
            animation: rotate 0.7s linear infinite;
        }
        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-completed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-canceled { background: #f8d7da; color: #721c24; }
        .status-processing { background: #cce7ff; color: #004085; }
        
        /* Order summary cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.07);
            text-align: center;
            border-left: 4px solid #2c3e50;
        }
        .stat-card.total { border-left-color: #27ae60; }
        .stat-card.completed { border-left-color: #3498db; }
        .stat-card.pending { border-left-color: #f39c12; }
        .stat-card.canceled { border-left-color: #e74c3c; }
        .stat-card h3 { margin: 0 0 10px 0; font-size: 14px; color: #666; text-transform: uppercase; }
        .stat-card .number { font-size: 28px; font-weight: 700; color: #2c3e50; }
        .stat-card.total .number { color: #27ae60; }
        .stat-card.completed .number { color: #3498db; }
        .stat-card.pending .number { color: #f39c12; }
        .stat-card.canceled .number { color: #e74c3c; }
        
        /* Enhanced Popup Styles */
        .popup-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .popup-content {
            background-color: white;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #2c3e50;
            color: white;
        }
        
        .popup-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }
        
        .popup-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0 5px;
        }
        
        .popup-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .item-detail {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            font-weight: 600;
            width: 150px;
            color: #2c3e50;
        }
        
        .payment-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .popup-footer {
            display: flex;
            justify-content: flex-end;
            padding: 15px 20px;
            background-color: #f9f9f9;
            border-top: 1px solid #eee;
        }
        
        .popup-btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.2s;
        }
        
        .popup-btn.cancel {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            color: #555;
        }
        
        .popup-btn.confirm {
            background-color: #28a745;
            border: 1px solid #28a745;
            color: white;
        }
        
        .popup-btn.print {
            background-color: #007bff;
            border: 1px solid #007bff;
            color: white;
        }
        
        .popup-btn.cancel:hover {
            background-color: #e0e0e0;
        }
        
        .popup-btn.confirm:hover {
            background-color: #218838;
            border-color: #218838;
        }
        
        .popup-btn.print:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        /* Profit highlighting */
        .profit-positive { color: #27ae60; font-weight: 600; }
        .profit-negative { color: #e74c3c; font-weight: 600; }
        
        /* Order items table in modal */
        .order-items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .order-items-table th,
        .order-items-table td {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            text-align: left;
        }
        
        .order-items-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .search-box {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            width: 250px;
            font-size: 14px;
        }

        /* Delete warning styles */
        .delete-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 12px;
            margin: 10px 0;
            text-align: center;
        }

        .delete-warning i {
            color: #f39c12;
            margin-right: 8px;
        }

        .delete-order-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin: 10px 0;
            border-left: 4px solid #e74c3c;
        }

        .delete-order-info div {
            margin: 4px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/pos" class="back-btn"><i class="fas fa-arrow-left"></i> Back to POS</a>
        <h2><i class="fas fa-shopping-cart"></i> Orders Management</h2>
        
        <!-- Order Statistics -->
        <div class="stats-cards" id="order-stats">
            <div class="stat-card total">
                <h3>Total Orders</h3>
                <div class="number" id="total-orders">0</div>
            </div>
            <div class="stat-card completed">
                <h3>Completed</h3>
                <div class="number" id="completed-orders">0</div>
            </div>
            <div class="stat-card pending">
                <h3>Pending</h3>
                <div class="number" id="pending-orders">0</div>
            </div>
            <div class="stat-card canceled">
                <h3>Canceled</h3>
                <div class="number" id="canceled-orders">0</div>
            </div>
        </div>
        
        <div class="filter-bar">
            <label for="status-filter">Status:</label>
            <select id="status-filter">
                <option value="">All Orders</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="canceled">Canceled</option>
            </select>
            
            <label for="date-filter">Order Date:</label>
            <input type="date" id="date-filter" max="" />
            
            <input type="text" id="search-input" class="search-box" placeholder="Search by Order ID or Customer..." />
            
            <button id="refresh-btn" title="Reset filters and refresh" style="padding: 7px 16px; font-weight: bold; background: #e74c3c; color: white; border: none; border-radius: 4px; margin-left: 10px;">
                <i class="fas fa-rotate-right"></i> Refresh
            </button>
        </div>
        
        <div id="orders-content" class="loading">Loading orders...</div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal-overlay" id="order-modal">
        <div class="modal-content" id="order-modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div id="order-modal-body"></div>
        </div>
    </div>
    
    <!-- Payment Details Modal -->
    <div class="modal-overlay" id="payment-modal">
        <div class="modal-content" id="payment-modal-content">
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
            <div id="payment-modal-body"></div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="popup-container" id="receipt-popup" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Order Receipt</h3>
                <button class="popup-close" onclick="closeReceiptPopup()">&times;</button>
            </div>
            <div class="popup-body" id="receipt-modal-body">
                <!-- Receipt content will be dynamically inserted here -->
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closeReceiptPopup()">Close</button>
                <button class="popup-btn print" onclick="printStyledReceipt(currentReceiptData)"><i class="fas fa-print"></i> Print Receipt</button>
            </div>
        </div>
    </div>

    <!-- Return Order Modal -->
    <div class="popup-container" id="return-order-popup" style="display: none;">
        <div class="popup-content" style="max-width: 800px;">
            <div class="popup-header">
                <h3><i class="fas fa-undo"></i> Process Order Return</h3>
                <button class="popup-close" onclick="closeReturnOrderPopup()">&times;</button>
            </div>
            <div class="popup-body" id="return-order-body">
                <!-- Return order content will be dynamically inserted here -->
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closeReturnOrderPopup()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="popup-btn confirm" onclick="processOrderReturn()" id="process-return-btn">
                    <i class="fas fa-check"></i> Process Return
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentReceiptData = null;
        let deleteOrderId = null;
        let currentReturnOrderId = null;
        let selectedReturnItems = [];
        let returnReason = '';
        let refundAmount = 0;

        async function loadOrders() {
            const content = document.getElementById('orders-content');
            const status = document.getElementById('status-filter').value;
            const date = document.getElementById('date-filter').value;
            const search = document.getElementById('search-input').value;

            let url = `/api/orders?`;
            if (status) url += `status=${encodeURIComponent(status)}&`;
            if (date) url += `date=${encodeURIComponent(date)}&`;
            if (search) url += `search=${encodeURIComponent(search)}&`;

            content.innerHTML = '<div class="loading">Loading orders...</div>';

            try {
                console.log('Loading orders from:', url);
                const response = await fetch(url);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response:', errorText);
                    throw new Error(`Server returned ${response.status}: ${errorText}`);
                }
                
                const orders = await response.json();
                console.log('Orders data received:', orders);
                
                if (!orders || !Array.isArray(orders)) {
                    throw new Error('Invalid response format from server');
                }

                if (!orders.length) {
                    content.innerHTML = '<div class="loading">No orders found for selected filters.</div>';
                    updateOrderStats([]);
                    return;
                }

                updateOrderStats(orders);
                renderOrdersTable(orders);
                
            } catch (error) {
                console.error('Full error details:', error);
                content.innerHTML = `
                    <div class="error">
                        <h3>Error Loading Orders</h3>
                        <p><strong>Error:</strong> ${error.message}</p>
                        <p>Please check the browser console for more details.</p>
                        <button onclick="loadOrders()" style="padding: 10px; margin: 10px 0; background: #e74c3c; color: white; border: none; border-radius: 4px;">
                            Try Again
                        </button>
                    </div>
                `;
            }
        }

        function updateOrderStats(orders) {
            const total = orders.length;
            const completed = orders.filter(o => o.status === 'completed').length;
            const pending = orders.filter(o => o.status === 'pending').length;
            const canceled = orders.filter(o => o.status === 'canceled').length;

            document.getElementById('total-orders').textContent = total;
            document.getElementById('completed-orders').textContent = completed;
            document.getElementById('pending-orders').textContent = pending;
            document.getElementById('canceled-orders').textContent = canceled;
        }

        function renderOrdersTable(orders) {
            let html = `<table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>`;

            orders.forEach(order => {
                const statusClass = `status-${order.status}`;
                const statusText = order.status.charAt(0).toUpperCase() + order.status.slice(1);
                const canDelete = order.status === 'pending' || order.status === 'canceled';
                
                html += `<tr>
                    <td><strong>${order.order_id}</strong></td>
                    <td>${order.customer_name || 'Walk-in Customer'}</td>
                    <td>Rs.${parseFloat(order.subtotal).toFixed(2)}</td>
                    <td>Rs.${parseFloat(order.discount).toFixed(2)}</td>
                    <td><strong>Rs.${parseFloat(order.total).toFixed(2)}</strong></td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td class="actions">
                        <a onclick="showOrderDetails(${order.id})"><i class="fas fa-eye"></i> View</a>
                        <a onclick="showPaymentDetails(${order.id})"><i class="fas fa-credit-card"></i> Payment</a>
                        <a onclick="showReceipt(${order.id})"><i class="fas fa-receipt"></i> Receipt</a>

                        ${order.status === 'pending' ? `
                            <a onclick="updateOrderStatus(${order.id}, 'processing')" style="color:#f39c12;">
                                <i class="fas fa-cog"></i> Process
                            </a>
                            <a onclick="updateOrderStatus(${order.id}, 'completed')" style="color:#27ae60;">
                                <i class="fas fa-check"></i> Complete
                            </a>
                        ` : ''}

                        ${order.status === 'processing' ? `
                            <a onclick="updateOrderStatus(${order.id}, 'completed')" style="color:#27ae60;">
                                <i class="fas fa-check"></i> Complete
                            </a>
                        ` : ''}

                        ${canDelete ? `
                            <a onclick="deleteOrder(${order.id})" style="color:#e74c3c;">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        ` : ''}

                        ${order.status === 'completed' ? `
                            <a onclick="showReturnOrder(${order.id})" style="color:#17a2b8;">
                                <i class="fas fa-undo"></i> Return
                            </a>
                        ` : ''}
                    </td>
                </tr>`;
            });

            html += `</tbody></table>`;
            document.getElementById('orders-content').innerHTML = html;
        }


        async function showOrderDetails(id) {
            const modal = document.getElementById('order-modal');
            const body = document.getElementById('order-modal-body');
            body.innerHTML = '<div class="loading">Loading order details...</div>';
            modal.style.display = 'flex';
            
            try {
                const res = await fetch(`/api/orders/${id}`);
                if (!res.ok) throw new Error('Failed to fetch order details');
                const order = await res.json();
                
                let html = `
                    <h3>Order #${order.order_id}</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <p><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleString()}</p>
                            <p><strong>Status:</strong> <span class="status-badge status-${order.status}">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span></p>
                            <p><strong>Customer ID:</strong> ${order.customer_id || 'N/A'}</p>
                        </div>
                        <div>
                            <p><strong>Customer Name:</strong> ${order.customer_name || 'Walk-in Customer'}</p>
                            <p><strong>Phone:</strong> ${order.customer_phone || 'N/A'}</p>
                            <p><strong>Created By:</strong> ${order.created_by_name || 'System'}</p>
                        </div>
                    </div>
                    
                    <h4>Order Summary</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                            <p><strong>Subtotal:</strong> Rs.${parseFloat(order.subtotal).toFixed(2)}</p>
                            <p><strong>Discount:</strong> Rs.${parseFloat(order.discount).toFixed(2)}</p>
                            <p><strong>Total Amount:</strong> Rs.${parseFloat(order.total).toFixed(2)}</p>
                        </div>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                            <p><strong>Payment Status:</strong> ${order.payment_status || 'N/A'}</p>
                            <p><strong>Payment Method:</strong> ${order.payment_method || 'N/A'}</p>
                            <p><strong>Payment Date:</strong> ${order.payment_date ? new Date(order.payment_date).toLocaleString() : 'N/A'}</p>
                        </div>
                    </div>
                    
                    <h4>Order Items</h4>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Cost Price</th>
                                <th>Line Total</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                let totalProfit = 0;
                order.items.forEach(item => {
                    const profit = (item.unit_price - (item.cost || 0)) * item.quantity;
                    totalProfit += profit;
                    const profitClass = profit >= 0 ? 'profit-positive' : 'profit-negative';
                    
                    html += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>Rs.${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>Rs.${parseFloat(item.cost || 0).toFixed(2)}</td>
                            <td>Rs.${parseFloat(item.line_total).toFixed(2)}</td>
                            <td class="${profitClass}">Rs.${parseFloat(profit).toFixed(2)}</td>
                        </tr>
                    `;
                });
                
                const totalProfitClass = totalProfit >= 0 ? 'profit-positive' : 'profit-negative';
                
                html += `
                        </tbody>
                        <tfoot>
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td colspan="5" style="text-align: right;">Total Profit:</td>
                                <td class="${totalProfitClass}">Rs.${parseFloat(totalProfit).toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                `;
                
                body.innerHTML = html;
            } catch (e) {
                console.error('Error loading order details:', e);
                body.innerHTML = '<div class="error">Error loading order details.</div>';
            }
        }

        async function showPaymentDetails(orderId) {
            const modal = document.getElementById('payment-modal');
            const body = document.getElementById('payment-modal-body');
            body.innerHTML = '<div class="loading">Loading payment details...</div>';
            modal.style.display = 'flex';
            
            try {
                const res = await fetch(`/api/orders/${orderId}/payments`);
                if (!res.ok) throw new Error('Failed to fetch payment details');
                const payments = await res.json();
                
                let html = `<h3>Payment Details</h3>`;
                
                if (!payments.length) {
                    html += '<p>No payment records found for this order.</p>';
                } else {
                    payments.forEach(payment => {
                        html += `
                            <div class="payment-info">
                                <p><strong>Payment ID:</strong> ${payment.id}</p>
                                <p><strong>Amount:</strong> Rs.${parseFloat(payment.amount).toFixed(2)}</p>
                                <p><strong>Method:</strong> ${payment.payment_method}</p>
                                <p><strong>Status:</strong> ${payment.status}</p>
                                <p><strong>Date:</strong> ${new Date(payment.payment_date).toLocaleString()}</p>
                                ${payment.reference_number ? `<p><strong>Reference:</strong> ${payment.reference_number}</p>` : ''}
                                ${payment.cheque_number ? `<p><strong>Cheque No:</strong> ${payment.cheque_number}</p>` : ''}
                                ${payment.bank ? `<p><strong>Bank:</strong> ${payment.bank}</p>` : ''}
                                ${payment.remarks ? `<p><strong>Remarks:</strong> ${payment.remarks}</p>` : ''}
                                ${payment.amount_received ? `<p><strong>Amount Received:</strong> Rs.${parseFloat(payment.amount_received).toFixed(2)}</p>` : ''}
                                ${payment.balance ? `<p><strong>Balance:</strong> Rs.${parseFloat(payment.balance).toFixed(2)}</p>` : ''}
                            </div>
                        `;
                    });
                }
                
                body.innerHTML = html;
            } catch (e) {
                console.error('Error loading payment details:', e);
                body.innerHTML = '<div class="error">Error loading payment details.</div>';
            }
        }

        async function showReceipt(orderId) {
            const modal = document.getElementById('receipt-popup');
            const body = document.getElementById('receipt-modal-body');
            body.innerHTML = '<div class="loading">Loading receipt...</div>';
            modal.style.display = 'flex';
            
            try {
                const res = await fetch(`/api/orders/${orderId}/receipt`);
                if (!res.ok) throw new Error('Failed to fetch receipt');
                const receiptData = await res.json();
                
                if (receiptData.success) {
                    currentReceiptData = receiptData.receipt_data;
                    renderReceipt(receiptData.receipt_data);
                } else {
                    throw new Error(receiptData.message || 'Failed to load receipt');
                }
            } catch (e) {
                console.error('Error loading receipt:', e);
                body.innerHTML = '<div class="error">Error loading receipt: ' + e.message + '</div>';
            }
        }

        function renderReceipt(receiptData) {
            const body = document.getElementById('receipt-modal-body');
            
            let html = `
                <div style="text-align: center; margin-bottom: 15px; border-bottom: 1px dashed #ddd; padding-bottom: 10px;">
                    <h3 style="margin: 0; color: #2c3e50;">LUXURY STORE</h3>
                    <p style="margin: 3px 0; font-size: 12px; color: #666;">POS System Receipt</p>
                    <p style="margin: 3px 0; font-size: 11px; color: #666;">
                        ${receiptData.receipt_number} • ${new Date(receiptData.date).toLocaleString()}
                    </p>
                </div>

                <div style="margin: 10px 0; padding: 8px; background: #fffbea; border: 1px solid #f0e6a8; border-radius: 6px; font-size: 13px;">
                    <div><strong>Order ID:</strong> ${receiptData.order_number}</div>
                    <div><strong>Customer:</strong> ${receiptData.customer.name}</div>
                    <div><strong>Phone:</strong> ${receiptData.customer.phone}</div>
                </div>

                <div style="margin-top: 10px;">
                    <div style="display: grid; grid-template-columns: 3fr 1fr 1fr 1fr; gap: 6px; font-weight: 700; font-size: 12px; color: #666; padding: 6px 0;">
                        <div>ITEM (QTY)</div>
                        <div style="text-align:center">MARKET PRICE</div>
                        <div style="text-align:center">OUR PRICE</div>
                        <div style="text-align:right">TOTAL</div>
                    </div>
                    <div style="border-bottom: 1px dashed #eee; margin: 6px 0;"></div>
            `;
            
            // Add items
            receiptData.items.forEach(item => {
                html += `
                    <div style="display: grid; grid-template-columns: 3fr 1fr 1fr 1fr; gap: 6px; align-items: center; padding: 6px 0;">
                        <div style="font-size: 13px;">
                            ${item.name}
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">× ${item.quantity}</div>
                        </div>
                        <div style="color: #8a8f98; font-size: 12px; text-decoration: line-through; text-align: center;">
                            Rs.${parseFloat(item.market_price || item.price).toFixed(2)}
                        </div>
                        <div style="color: #2c3e50; font-weight: 700; font-size: 12px; text-align: center;">
                            Rs.${parseFloat(item.price).toFixed(2)}
                        </div>
                        <div style="text-align: right; font-weight: 700; color: #2c3e50;">
                            Rs.${parseFloat(item.total).toFixed(2)}
                        </div>
                    </div>
                    <div style="text-align: right; color: #28a745; font-size: 12px; margin-top: -6px; margin-bottom: 6px;">
                        Profit: Rs.${parseFloat(item.total_profit || 0).toFixed(2)}
                    </div>
                    <div style="border-bottom: 1px dashed #eee; margin: 6px 0;"></div>
                `;
            });
            
            // Add totals
            html += `
                </div>

                <div style="margin-top: 10px;">
                    <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px dashed #ddd; font-weight: 700;">
                        <div>Subtotal:</div>
                        <div>Rs.${parseFloat(receiptData.totals.subtotal).toFixed(2)}</div>
                    </div>
                    ${receiptData.totals.discount > 0 ? `
                    <div style="display: flex; justify-content: space-between; padding-top: 6px; font-weight: 600;">
                        <div>Discount:</div>
                        <div>- Rs.${parseFloat(receiptData.totals.discount).toFixed(2)}</div>
                    </div>
                    ` : ''}
                    <div style="display: flex; justify-content: space-between; padding-top: 8px; font-size: 15px; border-top: 1px solid #ddd;">
                        <div>GRAND TOTAL:</div>
                        <div>Rs.${parseFloat(receiptData.totals.total).toFixed(2)}</div>
                    </div>
                </div>

                <div style="font-weight: 700; font-size: 15px; color: #28a745; text-align: center; margin: 15px 0; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid rgba(40,167,69,0.15);">
                    YOUR TOTAL PROFIT = Rs.${parseFloat(receiptData.totals.total_profit || 0).toFixed(2)}
                </div>

                <div style="margin-top: 10px; font-size: 13px; background: #f8f9fa; padding: 12px; border-radius: 6px;">
                    <div style="margin: 4px 0;"><strong>Payment Method:</strong> ${receiptData.payment.method.toUpperCase()}</div>
            `;
            
            // Add payment method specific information
            if (receiptData.payment.method === 'cash') {
                html += `
                    <div style="margin: 4px 0;">Cash Received: Rs.${parseFloat(receiptData.payment.cash_received).toFixed(2)}</div>
                    <div style="margin: 4px 0;">Balance: Rs.${parseFloat(receiptData.payment.cash_balance).toFixed(2)}</div>
                `;
            } else if (receiptData.payment.method === 'card') {
                html += `
                    <div style="margin: 4px 0;">Reference: ${receiptData.payment.reference || 'N/A'}</div>
                    ${receiptData.payment.bank ? `<div style="margin: 4px 0;">Bank: ${receiptData.payment.bank}</div>` : ''}
                `;
            } else if (receiptData.payment.method === 'cheque') {
                html += `
                    <div style="margin: 4px 0;">Cheque No: ${receiptData.payment.cheque_no || 'N/A'}</div>
                    <div style="margin: 4px 0;">Bank: ${receiptData.payment.bank || 'N/A'}</div>
                    ${receiptData.payment.remarks ? `<div style="margin: 4px 0;">Remarks: ${receiptData.payment.remarks}</div>` : ''}
                `;
            } else if (receiptData.payment.method === 'credit') {
                html += `
                    <div style="margin: 4px 0;">Previous Balance: Rs.${parseFloat(receiptData.payment.current_balance).toFixed(2)}</div>
                    <div style="margin: 4px 0; font-weight: 700; color: #e74c3c;">New Balance: Rs.${parseFloat(receiptData.payment.new_balance).toFixed(2)}</div>
                `;
            }
            
            html += `
                </div>

                <div style="text-align: center; margin-top: 15px; font-weight: 700; color: #2c3e50;">
                    Thank you for your business!
                </div>
            `;
            
            body.innerHTML = html;
        }


        async function confirmDeleteOrder() {
            if (!deleteOrderId) return;

            try {
                const res = await fetch(`/api/orders/${deleteOrderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await res.json();

                if (!res.ok) {
                    throw new Error(result.message || 'Failed to delete order');
                }

                alert('Order deleted successfully!');
                closeDeleteConfirmPopup();
                loadOrders(); // Refresh the list
            } catch (error) {
                console.error('Delete error:', error);
                alert(error.message || 'Failed to delete order. Please try again.');
                closeDeleteConfirmPopup();
            }
        }

        async function updateOrderStatus(orderId, newStatus) {
            if (!confirm(`Are you sure you want to mark this order as ${newStatus}?`)) {
                return;
            }

            try {
                const res = await fetch(`/api/orders/${orderId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const result = await res.json();

                if (!res.ok) {
                    throw new Error(result.message || 'Failed to update order status');
                }

                alert(`Order status updated to ${newStatus} successfully!`);
                loadOrders(); // Refresh the list
            } catch (error) {
                console.error('Error updating order status:', error);
                alert(error.message || 'Failed to update order status. Please try again.');
            }
        }

        function printStyledReceipt(receiptData) {
            const receiptDataToUse = receiptData || currentReceiptData;
            
            if (!receiptDataToUse) {
                alert('No receipt data available');
                return;
            }

            const receiptContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt - ${receiptDataToUse.receipt_number}</title>
                    <meta name="viewport" content="width=device-width,initial-scale=1">
                    <style>
                        :root { --muted: #666; --accent: #2c3e50; --success: #28a745; --paper: #fff; --bg: #f8f9fa; }
                        body { 
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Courier New", monospace; 
                            margin: 0; 
                            padding: 12px; 
                            font-size: 13px;
                            max-width: 420px;
                            color: var(--accent);
                            background: white;
                        }
                        .header { 
                            text-align: center; 
                            margin-bottom: 10px;
                            border-bottom: 1px dashed #ddd;
                            padding-bottom: 8px;
                        }
                        .company-name { 
                            font-weight: 700; 
                            font-size: 18px;
                            margin-bottom: 3px;
                        }
                        .receipt-meta { font-size: 12px; color: var(--muted); margin-top: 4px; }
                        .customer-block {
                            margin: 10px 0;
                            padding: 8px;
                            background: #fffbea;
                            border: 1px solid #f0e6a8;
                            border-radius: 6px;
                            font-size: 13px;
                        }
                        .customer-block div { margin: 2px 0; }
                        .items-section {
                            margin-top: 8px;
                        }
                        .item-header {
                            display: grid;
                            grid-template-columns: 3fr 1fr 1fr 1fr;
                            gap: 6px;
                            font-weight: 700;
                            font-size: 12px;
                            color: var(--muted);
                            padding: 6px 0;
                        }
                        .line { border-bottom: 1px dashed #eee; margin: 6px 0; }
                        .item-row {
                            display: grid;
                            grid-template-columns: 3fr 1fr 1fr 1fr;
                            gap: 6px;
                            align-items: center;
                            padding: 6px 0;
                        }
                        .item-name { 
                            font-size: 13px;
                        }
                        .price-details { font-size: 12px; color: var(--muted); margin-top: 4px; }
                        .market-price-value { 
                            color: #8a8f98; 
                            font-size: 12px; 
                            text-decoration: line-through;
                            text-align: center;
                        }
                        .our-price-value { 
                            color: var(--accent); 
                            font-weight: 700; 
                            font-size: 12px;
                            text-align: center;
                        }
                        .total-price { 
                            text-align: right; 
                            font-weight: 700; 
                            color: var(--accent);
                        }
                        .line-profit { margin-top: -6px; margin-bottom: 6px; }
                        .totals-section { margin-top: 8px; }
                        .item-row.total-line { 
                            display: flex; 
                            justify-content: space-between; 
                            padding-top: 8px; 
                            border-top: 1px dashed #ddd; 
                            font-weight: 700; 
                        }
                        .small-muted { font-size: 12px; color: var(--muted); }
                        .profit-total {
                            font-weight: 700;
                            font-size: 15px;
                            color: var(--success);
                            text-align: center;
                            margin: 10px 0;
                            background-color: var(--bg);
                            padding: 8px;
                            border-radius: 6px;
                            border: 1px solid rgba(40,167,69,0.15);
                        }
                        .payment-info { 
                            margin-top: 8px; 
                            font-size: 13px; 
                            background: #f8f9fa;
                            padding: 10px;
                            border-radius: 6px;
                        }
                        .payment-info div { margin: 4px 0; }
                        .thank-you { 
                            text-align: center; 
                            margin-top: 10px; 
                            font-weight: 700; 
                            color: var(--accent);
                        }
                        .no-print { margin-top: 12px; text-align: center; }
                        button { 
                            padding: 8px 14px; 
                            border-radius: 6px; 
                            border: none; 
                            cursor: pointer; 
                            font-weight: 600;
                            transition: all 0.3s ease;
                        }
                        .btn-print { 
                            background: var(--accent); 
                            color: #fff; 
                            margin-right: 6px; 
                        }
                        .btn-print:hover { background: #1a252f; }
                        .btn-close { 
                            background: #e74c3c; 
                            color: #fff; 
                        }
                        .btn-close:hover { background: #c0392b; }
                        @media print {
                            body { margin: 0; padding: 6px; max-width: 320px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="company-name">LUXURY STORE</div>
                        <div class="small-muted">POS System Receipt</div>
                        <div class="receipt-meta">${receiptDataToUse.receipt_number} • ${new Date(receiptDataToUse.date).toLocaleString()}</div>
                    </div>

                    <div class="customer-block">
                        <div><strong>Customer:</strong> ${receiptDataToUse.customer.name}</div>
                        <div><strong>Phone:</strong> ${receiptDataToUse.customer.phone}</div>
                    </div>

                    <div class="items-section">
                        <div class="item-header">
                            <div>ITEM (QTY)</div>
                            <div style="text-align:center">MARKET PRICE</div>
                            <div style="text-align:center">OUR PRICE</div>
                            <div style="text-align:right">TOTAL</div>
                        </div>

                        <div class="line"></div>

                        ${receiptDataToUse.items.map(item => `
                            <div class="item-row">
                                <div class="item-name">
                                    ${item.name}
                                    <div class="price-details">× ${item.quantity}</div>
                                </div>
                                <div class="market-price market-price-value">
                                    Rs.${parseFloat(item.market_price || item.price).toFixed(2)}
                                </div>
                                <div class="our-price our-price-value">
                                    Rs.${parseFloat(item.price).toFixed(2)}
                                </div>
                                <div class="total-price our-price-value">
                                    Rs.${parseFloat(item.total).toFixed(2)}
                                </div>
                            </div>
                            <div class="price-details line-profit" style="text-align: right; color: #28a745; font-size: 12px;">
                                Profit: Rs.${parseFloat(item.total_profit || 0).toFixed(2)}
                            </div>
                            <div class="line"></div>
                        `).join('')}

                    </div>

                    <div class="totals-section">
                        <div class="item-row total-line">
                            <div>Subtotal:</div>
                            <div></div>
                            <div></div>
                            <div>Rs.${parseFloat(receiptDataToUse.totals.subtotal).toFixed(2)}</div>
                        </div>
                        ${receiptDataToUse.totals.discount > 0 ? `
                        <div class="item-row total-line" style="font-weight:600;">
                            <div>Discount:</div>
                            <div></div>
                            <div></div>
                            <div>- Rs.${parseFloat(receiptDataToUse.totals.discount).toFixed(2)}</div>
                        </div>
                        ` : ''}
                        <div class="item-row total-line" style="font-size:15px;">
                            <div>GRAND TOTAL:</div>
                            <div></div>
                            <div></div>
                            <div>Rs.${parseFloat(receiptDataToUse.totals.total).toFixed(2)}</div>
                        </div>
                    </div>

                    <div class="profit-total">
                        YOUR TOTAL PROFIT = Rs.${parseFloat(receiptDataToUse.totals.total_profit || 0).toFixed(2)}
                    </div>

                    <div class="payment-info">
                        <div><strong>Payment Method:</strong> ${receiptDataToUse.payment.method.toUpperCase()}</div>
                        ${receiptDataToUse.payment.method === 'cash' ? `
                            <div>Cash Received: Rs.${parseFloat(receiptDataToUse.payment.cash_received).toFixed(2)}</div>
                            <div>Balance: Rs.${parseFloat(receiptDataToUse.payment.cash_balance).toFixed(2)}</div>
                        ` : ''}
                        ${receiptDataToUse.payment.method === 'card' ? `
                            <div>Reference: ${receiptDataToUse.payment.reference || 'N/A'}</div>
                            ${receiptDataToUse.payment.bank ? `<div>Bank: ${receiptDataToUse.payment.bank}</div>` : ''}
                        ` : ''}
                        ${receiptDataToUse.payment.method === 'cheque' ? `
                            <div>Cheque No: ${receiptDataToUse.payment.cheque_no || 'N/A'}</div>
                            <div>Bank: ${receiptDataToUse.payment.bank || 'N/A'}</div>
                            ${receiptDataToUse.payment.remarks ? `<div>Remarks: ${receiptDataToUse.payment.remarks}</div>` : ''}
                        ` : ''}
                        ${receiptDataToUse.payment.method === 'credit' ? `
                            <div>Previous Balance: Rs.${parseFloat(receiptDataToUse.payment.current_balance).toFixed(2)}</div>
                            <div>New Balance: Rs.${parseFloat(receiptDataToUse.payment.new_balance).toFixed(2)}</div>
                        ` : ''}
                    </div>

                    <div class="thank-you">
                        Thank you for your business!
                    </div>

                    <div class="no-print">
                        <button class="btn-print" onclick="window.print()">🖨️ Print Receipt</button>
                        <button class="btn-close" onclick="window.close()">❌ Close</button>
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank', 'width=450,height=700,scrollbars=yes');
            printWindow.document.write(receiptContent);
            printWindow.document.close();
            
            // Auto-print after a short delay
            setTimeout(() => {
                try { 
                    printWindow.print(); 
                } catch (e) { 
                    console.log('Print may be blocked by browser:', e);
                }
            }, 500);
        }

        function closeModal() {
            document.getElementById('order-modal').style.display = 'none';
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').style.display = 'none';
        }

        function closeReceiptPopup() {
            document.getElementById('receipt-popup').style.display = 'none';
            currentReceiptData = null;
        }

        // Event listeners
        document.getElementById('status-filter').addEventListener('change', loadOrders);
        document.getElementById('date-filter').addEventListener('change', loadOrders);
        document.getElementById('search-input').addEventListener('input', loadOrders);

        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date-filter').setAttribute('max', today);
            loadOrders();
        });

        document.getElementById('refresh-btn').addEventListener('click', function() {
            const btn = this;
            btn.classList.add('refreshing');
            document.getElementById('status-filter').value = '';
            document.getElementById('date-filter').value = '';
            document.getElementById('search-input').value = '';
            loadOrders().then(() => {
                setTimeout(() => btn.classList.remove('refreshing'), 700);
            });
        });

        // Return Orders
        async function showReturnOrder(orderId) {
            currentReturnOrderId = orderId;
            selectedReturnItems = [];
            returnReason = '';
            refundAmount = 0;
            
            const modal = document.getElementById('return-order-popup');
            const body = document.getElementById('return-order-body');
            body.innerHTML = '<div class="loading">Loading order details...</div>';
            modal.style.display = 'flex';
            
            try {
                const res = await fetch(`/api/orders/${orderId}`);
                if (!res.ok) throw new Error('Failed to fetch order details');
                const order = await res.json();
                
                renderReturnOrderForm(order);
            } catch (e) {
                console.error('Error loading order for return:', e);
                body.innerHTML = '<div class="error">Error loading order details: ' + e.message + '</div>';
            }
        }

        function renderReturnOrderForm(order) {
            const body = document.getElementById('return-order-body');
            
            let html = `
                <div style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; color: #2c3e50;">Order #${order.order_id}</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <p><strong>Customer:</strong> ${order.customer_name}</p>
                            <p><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <p><strong>Original Total:</strong> Rs.${parseFloat(order.total).toFixed(2)}</p>
                            <p><strong>Payment Method:</strong> ${order.payment_method || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Return Reason:</label>
                    <select id="return-reason" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" onchange="updateReturnReason(this.value)">
                        <option value="">Select Reason</option>
                        <option value="defective">Defective Product</option>
                        <option value="wrong_item">Wrong Item Received</option>
                        <option value="damaged">Damaged During Delivery</option>
                        <option value="not_as_described">Not as Described</option>
                        <option value="customer_change_mind">Customer Changed Mind</option>
                        <option value="other">Other</option>
                    </select>
                    <textarea id="return-reason-other" placeholder="Please specify reason..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-top: 8px; display: none;" oninput="updateReturnReason(this.value)"></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <h5 style="margin-bottom: 10px;">Select Items to Return:</h5>
                    <div style="border: 1px solid #e0e0e0; border-radius: 6px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e0e0e0; width: 30px;"></th>
                                    <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e0e0e0;">Product</th>
                                    <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e0e0e0;">Original Qty</th>
                                    <th style="padding: 10px; text-align: center; border-bottom: 1px solid #e0e0e0;">Return Qty</th>
                                    <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e0e0e0;">Unit Price</th>
                                    <th style="padding: 10px; text-align: right; border-bottom: 1px solid #e0e0e0;">Refund Amount</th>
                                </tr>
                            </thead>
                            <tbody id="return-items-table">
            `;
            
            order.items.forEach((item, index) => {
                html += `
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td style="padding: 10px;">
                            <input type="checkbox" id="item-${index}" onchange="toggleReturnItem(${index}, ${item.id}, '${item.product_name}', ${item.quantity}, ${item.unit_price})">
                        </td>
                        <td style="padding: 10px;">
                            <label for="item-${index}" style="cursor: pointer;">
                                ${item.product_name}
                            </label>
                        </td>
                        <td style="padding: 10px; text-align: center;">${item.quantity}</td>
                        <td style="padding: 10px; text-align: center;">
                            <input type="number" id="return-qty-${index}" min="1" max="${item.quantity}" value="${item.quantity}" 
                                style="width: 60px; padding: 4px; text-align: center; border: 1px solid #ddd; border-radius: 3px;"
                                onchange="updateReturnQuantity(${index}, ${item.unit_price})" disabled>
                        </td>
                        <td style="padding: 10px; text-align: right;">Rs.${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td style="padding: 10px; text-align: right;">
                            <span id="refund-amount-${index}">Rs.${parseFloat(item.unit_price * item.quantity).toFixed(2)}</span>
                        </td>
                    </tr>
                `;
            });
            
            html += `
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 16px; font-weight: 600;">
                        <span>Total Refund Amount:</span>
                        <span id="total-refund-amount">Rs.0.00</span>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Refund Method:</label>
                    <select id="refund-method" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="original">Original Payment Method</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card Refund</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="store_credit">Store Credit</option>
                    </select>
                </div>

                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 12px; margin-bottom: 15px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i>
                    <strong> Note:</strong> Processing this return will:
                    <ul style="margin: 8px 0 0 20px;">
                        <li>Update inventory stock for returned items</li>
                        <li>Create a refund record</li>
                        <li>Update order status if all items are returned</li>
                        <li>This action cannot be undone</li>
                    </ul>
                </div>
            `;
            
            body.innerHTML = html;
            updateTotalRefundAmount();
        }

        function updateReturnReason(reason) {
            returnReason = reason;
            const otherTextarea = document.getElementById('return-reason-other');
            if (reason === 'other') {
                otherTextarea.style.display = 'block';
                otherTextarea.required = true;
            } else {
                otherTextarea.style.display = 'none';
                otherTextarea.required = false;
                if (reason) {
                    returnReason = reason;
                }
            }
        }

        function toggleReturnItem(index, itemId, productName, maxQuantity, unitPrice) {
            const checkbox = document.getElementById(`item-${index}`);
            const quantityInput = document.getElementById(`return-qty-${index}`);
            
            if (checkbox.checked) {
                quantityInput.disabled = false;
                quantityInput.value = maxQuantity;
                
                selectedReturnItems.push({
                    index: index,
                    itemId: itemId,
                    productName: productName,
                    quantity: maxQuantity,
                    maxQuantity: maxQuantity,
                    unitPrice: unitPrice,
                    refundAmount: unitPrice * maxQuantity
                });
            } else {
                quantityInput.disabled = true;
                quantityInput.value = maxQuantity;
                
                selectedReturnItems = selectedReturnItems.filter(item => item.index !== index);
            }
            
            updateTotalRefundAmount();
        }

        function updateReturnQuantity(index, unitPrice) {
            const quantityInput = document.getElementById(`return-qty-${index}`);
            const refundAmountSpan = document.getElementById(`refund-amount-${index}`);
            const quantity = parseInt(quantityInput.value) || 0;
            const maxQuantity = selectedReturnItems.find(item => item.index === index)?.maxQuantity || 0;
            
            if (quantity > maxQuantity) {
                quantityInput.value = maxQuantity;
                quantity = maxQuantity;
            }
            
            if (quantity < 1) {
                quantityInput.value = 1;
                quantity = 1;
            }
            
            const refundAmount = unitPrice * quantity;
            refundAmountSpan.textContent = `Rs.${refundAmount.toFixed(2)}`;
            
            const itemIndex = selectedReturnItems.findIndex(item => item.index === index);
            if (itemIndex !== -1) {
                selectedReturnItems[itemIndex].quantity = quantity;
                selectedReturnItems[itemIndex].refundAmount = refundAmount;
            }
            
            updateTotalRefundAmount();
        }

        function updateTotalRefundAmount() {
            refundAmount = selectedReturnItems.reduce((total, item) => total + item.refundAmount, 0);
            document.getElementById('total-refund-amount').textContent = `Rs.${refundAmount.toFixed(2)}`;
            
            // Update process button state
            const processBtn = document.getElementById('process-return-btn');
            processBtn.disabled = selectedReturnItems.length === 0 || !returnReason || refundAmount === 0;
        }

        async function processOrderReturn() {
            if (selectedReturnItems.length === 0 || !returnReason || refundAmount === 0) {
                alert('Please select items to return and provide a reason.');
                return;
            }
            
            const refundMethod = document.getElementById('refund-method').value;
            
            if (!confirm(`Are you sure you want to process this return for Rs.${refundAmount.toFixed(2)}?`)) {
                return;
            }
            
            const processBtn = document.getElementById('process-return-btn');
            processBtn.disabled = true;
            processBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            try {
                const returnData = {
                    order_id: currentReturnOrderId,
                    return_reason: returnReason,
                    refund_amount: refundAmount,
                    refund_method: refundMethod,
                    return_items: selectedReturnItems.map(item => ({
                        order_item_id: item.itemId,
                        quantity: item.quantity,
                        refund_amount: item.refundAmount
                    }))
                };
                
                const res = await fetch('/api/orders/return', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(returnData)
                });
                
                const result = await res.json();
                
                if (!res.ok) {
                    throw new Error(result.message || 'Failed to process return');
                }
                
                alert('Return processed successfully!');
                closeReturnOrderPopup();
                loadOrders(); // Refresh the orders list
                
            } catch (error) {
                console.error('Error processing return:', error);
                alert('Error processing return: ' + error.message);
                processBtn.disabled = false;
                processBtn.innerHTML = '<i class="fas fa-check"></i> Process Return';
            }
        }

        function closeReturnOrderPopup() {
            document.getElementById('return-order-popup').style.display = 'none';
            currentReturnOrderId = null;
            selectedReturnItems = [];
            returnReason = '';
            refundAmount = 0;
        }
    </script>
</body>
</html>