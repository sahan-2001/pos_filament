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
    <script>
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

        // Live filtering: reload bills when filter changes
        document.getElementById('status-filter').addEventListener('change', loadDraftBills);
        document.getElementById('date-filter').addEventListener('change', loadDraftBills);

        // Set max date to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date-filter').setAttribute('max', today);
        });

        // Refresh button resets filters and reloads bills
        document.getElementById('refresh-btn').addEventListener('click', function() {
            const btn = this;
            btn.classList.add('refreshing');
            document.getElementById('status-filter').value = '';
            document.getElementById('date-filter').value = '';
            loadDraftBills().then(() => {
                setTimeout(() => btn.classList.remove('refreshing'), 700);
            });
        });

        // Initial load
        loadDraftBills();
    </script>
</body>
</html>