<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Draft Bills - Luxury POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; margin: 0; }
        .container { max-width: 1200px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(44,62,80,0.07); padding: 30px; }
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
            background: #fff; border-radius: 8px; padding: 30px; max-width: 500px; width: 95vw; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
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
        
        /* Enhanced Payment Popup Styles */
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
        
        .payment-button-group {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.25rem;
        }
        
        .payment-btn {
            padding: 0.4rem 1rem;
            border: 1px solid #ccc;
            background-color: #f8f8f8;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .payment-btn:hover {
            background-color: #e2e2e2;
        }
        
        .payment-btn.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .payment-section {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .payment-section.active {
            display: block;
        }
        
        .price-input {
            width: 120px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: right;
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
        
        .popup-btn.cancel:hover {
            background-color: #e0e0e0;
        }
        
        .popup-btn.confirm:hover {
            background-color: #218838;
            border-color: #218838;
        }
        
        .negative-balance {
            color: red;
            font-weight: bold;
        }
        
        .customer-info-row {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/pos" class="back-btn"><i class="fas fa-arrow-left"></i> Back to POS</a>
        <h2><i class="fas fa-file-alt"></i> Draft Bills</h2>
        <div class="filter-bar">
            <label for="status-filter">Status:</label>
            <select id="status-filter">
                <option value="">All</option>
                <option value="draft">Draft</option>
                <option value="paid">Paid</option>
                <option value="partially_paid">Partially Paid</option>
                <option value="canceled">Canceled</option>
            </select>
            <label for="date-filter">Created Date:</label>
            <input type="date" id="date-filter" max="" />
            <button id="refresh-btn" title="Reset filters and refresh" style="padding: 7px 16px; font-weight: bold; background: #e74c3c; color: white; border: none; border-radius: 4px; margin-left: 10px;">
                <i class="fas fa-rotate-right"></i> Refresh
            </button>
        </div>
        <div id="draft-bills-content" class="loading">Loading draft bills...</div>
    </div>

    <div class="modal-overlay" id="bill-modal">
        <div class="modal-content" id="bill-modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div id="bill-modal-body"></div>
        </div>
    </div>
    
    <!-- Enhanced Payment Popup -->
    <div id="payment-popup" class="popup-container" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Process Payment</h3>
                <button class="popup-close" onclick="closePaymentPopup()">&times;</button>
            </div>
            <div class="popup-body" id="payment-modal-body">
                <!-- Content will be dynamically inserted here -->
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closePaymentPopup()">Cancel</button>
                <button class="popup-btn confirm" onclick="submitPayment()">Complete Payment</button>
            </div>
        </div>
    </div>

    <script>
        let currentBill = null;
        let currentCustomer = null;
        
        async function loadDraftBills() {
            const content = document.getElementById('draft-bills-content');
            const status = document.getElementById('status-filter').value;
            const date = document.getElementById('date-filter').value;

            let url = `/api/draft-invoices?`;
            if (status) url += `status=${encodeURIComponent(status)}&`;
            if (date) url += `date=${encodeURIComponent(date)}&`;

            content.innerHTML = '<div class="loading">Loading draft bills...</div>';

            try {
                const res = await fetch(url);
                if (!res.ok) throw new Error('Failed to fetch draft bills');
                const bills = await res.json();
                if (!bills.length) {
                    content.innerHTML = '<div class="loading">No bills found for selected filters.</div>';
                    return;
                }

                let html = `<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>`;

                bills.forEach(bill => {
                    const canPay = bill.status === 'draft' || bill.status === 'partially_paid';
                    html += `<tr>
                        <td>${bill.id}</td>
                        <td>${new Date(bill.created_at).toLocaleString()}</td>
                        <td>${bill.customer_id || 'N/A'}</td>
                        <td>${bill.customer_name || 'N/A'}</td>
                        <td>Rs.${parseFloat(bill.total).toFixed(2)}</td>
                        <td>Rs.${parseFloat(bill.discount).toFixed(2)}</td>
                        <td>${bill.status || 'N/A'}</td>
                        <td>${bill.items_count}</td>
                        <td class="actions">
                            <a onclick="showBillDetails(${bill.id})"><i class="fas fa-eye"></i> View</a>
                            ${canPay ? `<a onclick="showPayPopup(${bill.id}, '${bill.customer_id || ''}', ${bill.total}, ${bill.discount})"><i class="fas fa-money-bill-wave"></i> Pay</a>` : ''}
                        </td>
                    </tr>`;
                });

                html += `</tbody></table>`;
                content.innerHTML = html;
            } catch (e) {
                content.innerHTML = `<div class="error">Error loading draft bills.</div>`;
            }
        }

        async function showBillDetails(id) {
            const modal = document.getElementById('bill-modal');
            const body = document.getElementById('bill-modal-body');
            body.innerHTML = '<div class="loading">Loading bill details...</div>';
            modal.style.display = 'flex';
            try {
                const res = await fetch(`/api/draft-invoices/${id}`);
                if (!res.ok) throw new Error('Failed to fetch bill');
                const bill = await res.json();
                let html = `
                    <h3>Draft Bill #${bill.id}</h3>
                    <p><strong>Date:</strong> ${new Date(bill.created_at).toLocaleString()}</p>
                    <p><strong>Customer ID:</strong> ${bill.customer_id || 'N/A'}</p>
                    <p><strong>Customer:</strong> ${bill.customer_name || 'N/A'}</p>
                    <p><strong>Total:</strong> Rs.${parseFloat(bill.total).toFixed(2)}</p>
                    <p><strong>Discount:</strong> Rs.${parseFloat(bill.discount).toFixed(2)}</p>
                    <h4>Items</h4>
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                bill.items.forEach(item => {
                    html += `<tr>
                        <td>${item.product_id}</td>
                        <td>${item.name}</td>
                        <td>${item.quantity}</td>
                        <td>Rs.${parseFloat(item.price).toFixed(2)}</td>
                        <td>Rs.${parseFloat(item.total).toFixed(2)}</td>
                    </tr>`;
                });
                html += `</tbody></table>`;
                body.innerHTML = html;
            } catch (e) {
                body.innerHTML = `<div class="error">Error loading bill details.</div>`;
            }
        }

        function closeModal() {
            document.getElementById('bill-modal').style.display = 'none';
        }

        async function showPayPopup(id, customer_id, total, discount) {
            currentBill = { id, customer_id, total, discount };
            const modal = document.getElementById('payment-popup');
            const body = document.getElementById('payment-modal-body');
            
            // Fetch customer details if available
            currentCustomer = null;
            let customerBalance = 0;
            let customerInfoHtml = '';
            
            if (customer_id && customer_id !== 'N/A' && customer_id !== '') {
                try {
                    const res = await fetch(`/api/customers/${customer_id}`);
                    if (res.ok) {
                        currentCustomer = await res.json();
                        customerBalance = parseFloat(currentCustomer.remaining_balance || 0);
                        
                        customerInfoHtml = `
                            <div class="customer-info-row">
                                <div><strong>Customer ID:</strong> ${currentCustomer.id}</div>
                                <div><strong>Name:</strong> ${currentCustomer.name}</div>
                                <div><strong>Phone:</strong> ${currentCustomer.phone_1}</div>
                                <div><strong>Current Balance:</strong> Rs.${customerBalance.toFixed(2)}</div>
                            </div>
                        `;
                    }
                } catch (e) {
                    console.error('Error fetching customer details:', e);
                }
            }
            
            body.innerHTML = `
                <div class="item-detail">
                    <span class="detail-label">Order Total:</span>
                    <span id="payment-total-amount">Rs.${parseFloat(total).toFixed(2)}</span>
                </div>
                
                ${customerInfoHtml}
                
                <div class="item-detail">
                    <span class="detail-label">Payment Method:</span>
                    <div id="payment-method-buttons" class="payment-button-group">
                        <button type="button" class="payment-btn active" data-method="cash" onclick="selectPaymentMethod('cash')">Cash</button>
                        <button type="button" class="payment-btn" data-method="card" onclick="selectPaymentMethod('card')">Card</button>
                        <button type="button" class="payment-btn" data-method="cheque" onclick="selectPaymentMethod('cheque')">Cheque</button>
                        ${customer_id && customer_id !== 'N/A' && customer_id !== '' ? 
                            `<button type="button" class="payment-btn" data-method="credit" onclick="selectPaymentMethod('credit')">Credit</button>` : ''}
                    </div>
                    <input type="hidden" id="payment-method" value="cash" />
                </div>
                
                <!-- Cash Payment Section -->
                <div id="cash-payment-section" class="payment-section active">
                    <div class="item-detail">
                        <span class="detail-label">Cash Received:</span>
                        <input type="number" id="cash-received" class="price-input" value="${parseFloat(total).toFixed(2)}" min="0" step="0.01" 
                            oninput="calculateCashBalance()" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Balance:</span>
                        <span id="cash-balance">Rs.0.00</span>
                    </div>
                </div>
                
                <!-- Card Payment Section -->
                <div id="card-payment-section" class="payment-section">
                    <div class="item-detail">
                        <span class="detail-label">Reference No:</span>
                        <input type="text" id="card-reference" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Bank (Optional):</span>
                        <input type="text" id="card-bank" />
                    </div>
                </div>
                
                <!-- Cheque Payment Section -->
                <div id="cheque-payment-section" class="payment-section">
                    <div class="item-detail">
                        <span class="detail-label">Cheque No:</span>
                        <input type="text" id="cheque-number" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Bank:</span>
                        <input type="text" id="cheque-bank" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Remarks (Optional):</span>
                        <input type="text" id="cheque-remarks" />
                    </div>
                </div>
                
                <!-- Credit Payment Section -->
                <div id="credit-payment-section" class="payment-section">
                    <div class="item-detail">
                        <span class="detail-label">Current Balance:</span>
                        <span id="current-credit-balance">Rs.${customerBalance.toFixed(2)}</span>
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">New Balance:</span>
                        <span id="new-credit-balance">Rs.${(customerBalance + parseFloat(total)).toFixed(2)}</span>
                    </div>
                </div>
            `;
            
            // Initialize cash balance calculation
            calculateCashBalance();
            modal.style.display = 'flex';
            
            // Focus on cash received field
            document.getElementById('cash-received').focus();
            document.getElementById('cash-received').select();
        }

        function closePaymentPopup() {
            document.getElementById('payment-popup').style.display = 'none';
            currentBill = null;
            currentCustomer = null;
        }

        function selectPaymentMethod(method) {
            // Update active button
            document.querySelectorAll('.payment-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.payment-btn[data-method="${method}"]`).classList.add('active');
            
            // Update hidden field
            document.getElementById('payment-method').value = method;
            
            // Hide all payment sections
            document.querySelectorAll('.payment-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(`${method}-payment-section`).classList.add('active');
        }

        function calculateCashBalance() {
            if (!currentBill) return;
            
            const received = parseFloat(document.getElementById('cash-received').value) || 0;
            const balance = received - currentBill.total;
            
            const balanceElement = document.getElementById('cash-balance');
            balanceElement.textContent = `Rs.${balance.toFixed(2)}`;
            
            if (balance < 0) {
                balanceElement.classList.add('negative-balance');
            } else {
                balanceElement.classList.remove('negative-balance');
            }
        }

        async function submitPayment() {
            if (!currentBill) return;
            
            const method = document.getElementById('payment-method').value;
            const paymentData = {
                method,
                amount: currentBill.total,
                remarks: ''
            };
            
            try {
                // Validate based on payment method
                if (method === 'cash') {
                    const received = parseFloat(document.getElementById('cash-received').value) || 0;
                    if (received < currentBill.total) {
                        throw new Error('Amount received is less than total');
                    }
                    paymentData.amount_received = received;
                    paymentData.balance = received - currentBill.total;
                }
                else if (method === 'card') {
                    const reference = document.getElementById('card-reference').value.trim();
                    if (!reference) {
                        throw new Error('Reference number is required for card payment');
                    }
                    paymentData.reference = reference;
                    paymentData.bank = document.getElementById('card-bank').value.trim();
                }
                else if (method === 'cheque') {
                    const chequeNo = document.getElementById('cheque-number').value.trim();
                    const bank = document.getElementById('cheque-bank').value.trim();
                    if (!chequeNo || !bank) {
                        throw new Error('Cheque number and bank are required');
                    }
                    paymentData.cheque_no = chequeNo;
                    paymentData.bank = bank;
                    paymentData.remarks = document.getElementById('cheque-remarks').value.trim();
                }
                else if (method === 'credit') {
                    if (!currentBill.customer_id) {
                        throw new Error('No customer selected for credit payment');
                    }
                    paymentData.customer_id = currentBill.customer_id;
                }
                
                // Show loading state
                const confirmBtn = document.querySelector('.popup-btn.confirm');
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Processing...';
                
                // Submit payment
                const res = await fetch(`/api/draft-invoices/${currentBill.id}/pay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(paymentData)
                });
                
                if (!res.ok) {
                    const error = await res.json();
                    throw new Error(error.message || 'Payment failed');
                }
                
                // Success
                alert('Payment processed successfully!');
                closePaymentPopup();
                loadDraftBills();
                
            } catch (error) {
                console.error('Payment error:', error);
                alert(error.message || 'Payment failed. Please try again.');
                
                // Re-enable button
                const confirmBtn = document.querySelector('.popup-btn.confirm');
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Complete Payment';
                }
            }
        }

        document.getElementById('status-filter').addEventListener('change', loadDraftBills);
        document.getElementById('date-filter').addEventListener('change', loadDraftBills);

        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date-filter').setAttribute('max', today);
            loadDraftBills();
        });

        document.getElementById('refresh-btn').addEventListener('click', function() {
            const btn = this;
            btn.classList.add('refreshing');
            document.getElementById('status-filter').value = '';
            document.getElementById('date-filter').value = '';
            loadDraftBills().then(() => {
                setTimeout(() => btn.classList.remove('refreshing'), 700);
            });
        });
    </script>
</body>
</html>