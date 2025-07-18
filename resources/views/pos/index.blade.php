<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Luxury POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        :root {
        --primary-color: #2c3e50;
        --secondary-color: #b38b6d;
        --accent-color: #e74c3c;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --success-color: #28a745;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f5f5f5;
            color: var(--dark-color);
            margin: 0;
            padding: 0;
        }

        .container {
            display: grid;
            grid-template-columns: 3fr 2fr; 
            gap: 30px;
            max-width: 90vw;
            width: 90vw;
            margin: 0 auto;
            padding: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 12px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.07);
            padding: 10px 18px;
            min-height: 48px;
            width: 100%;
            box-sizing: border-box;
        }
        .user-info span {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.08rem;
        }
        .user-info #live-datetime {
            font-weight: 500;
            color: var(--dark-color);
            font-size: 1.08rem;
        }
        .user-info .btn-nav {
            background: var(--primary-color);
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.97rem;
            box-shadow: 0 1px 4px rgba(44,62,80,0.07);
            transition: background 0.2s;
            border: none;
            outline: none;
            display: inline-block;
        }
        .user-info .btn-nav:nth-child(1) { background: var(--primary-color);}
        .user-info .btn-nav:nth-child(2) { background: var(--success-color);}
        .user-info .btn-nav:nth-child(3) { background: var(--secondary-color);}
        .user-info .btn-nav:nth-child(4) { background: #23272b;}
        .user-info .btn-nav:nth-child(5) { background: var(--accent-color);}
        .user-info .btn-nav:hover {
            filter: brightness(0.92);
            box-shadow: 0 2px 8px rgba(44,62,80,0.12);
        }
        .user-info .btn-nav:active {
            filter: brightness(0.85);
        }
        .user-info .fas.fa-user {
            margin-right: 6px;
            color: var(--secondary-color);
        }
        .user-info > div {
            display: flex;
            gap: 7px;
            margin-left: auto;
        }
        .user-info .btn-nav.back-dashboard {
            background: var(--light-color);
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .user-info .btn-nav.back-dashboard:hover {
            background: var(--primary-color);
            color: #fff;
            box-shadow: 0 2px 8px rgba(44,62,80,0.12);
        }

        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
                padding: 0;
            }
            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 10px 8px;
            }
            .user-info > div {
                margin-left: 0;
                gap: 5px;
            }
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            margin: 0;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }

        h2 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
        }

        .product-section {
            grid-column: 1 / 2;
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .cart-section{
            grid-column: 2 / 3;
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .customer-section {
            grid-column: 1 / span 2;
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .header-section {
            grid-column: 1 / span 2;
            background-color: white;
        }

        #customer-select,
        #customer-phone,
        #customer-id-search {
            width: 90%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        #product-search {
            width: 96.2%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .id-search-container {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        #reset-customer-btn {
            padding: 10px 15px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        #reset-customer-btn:hover {
            background-color: #c0392b;
        }

        .customer-info {
            background: #f9f9f9;
            padding: 10px 15px;
            margin-top: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-weight: 500;
        }

        #customer-info span {
            display: inline-block;
            margin-right: 15px;
            font-weight: 500;
        }

        .negative-balance {
            color: red;
            font-weight: bold;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr); 
            gap: 12px; 
            margin-top: 20px;
        }

        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px; 
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            user-select: none;
        }

        .product-card img {
            max-width: 100%;
            height: 80px; 
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 8px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--secondary-color);
        }

        .product-card h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .product-card .price {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .remove-btn {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--accent-color);
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .remove-btn:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .total-section {
            margin-top: auto;
            padding-top: 20px;
            border-top: 2px solid var(--secondary-color);
        }

        .grand-total {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .checkout-btn {
            background-color: var(--success-color);
            color: white;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'Montserrat', sans-serif;
        }

        .checkout-btn:hover {
            background-color: #218838;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .qty-btn {
            background-color: #e0e0e0;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            user-select: none;
        }

        .qty-btn:hover {
            background-color: #d0d0d0;
        }

        .empty-cart {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .error {
            text-align: center;
            padding: 20px;
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            max-width: 300px;
        }

        .notification.success {
            background-color: var(--success-color);
        }

        .notification.error {
            background-color: var(--accent-color);
        }

        .notification.fade-out {
            animation: fadeOut 0.5s ease-out;
        }

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
            background-color: var(--primary-color);
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
            color: var(--primary-color);
        }

        .quantity-selector {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        .quantity-selector label {
            font-weight: 600;
            margin-right: 15px;
            width: 150px;
            color: var(--primary-color);
        }

        .qty-control {
            display: flex;
            align-items: center;
        }

        #popup-item-qty {
            width: 60px;
            text-align: center;
            padding: 8px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
            background-color: var(--success-color);
            border: 1px solid var(--success-color);
            color: white;
        }

        .popup-btn.cancel:hover {
            background-color: #e0e0e0;
        }

        .popup-btn.confirm:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .fraction-buttons {
            display: flex;
            gap: 10px;
            margin-left: 15px;
        }

        .fraction-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;   
            min-width: 40px;    
            min-height: 25px;     
            border-radius: 8px;  
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1.2rem;  
            user-select: none;
        }

        .fraction-btn:hover {
            background-color: #1a73e8; 
        }

        .price-input {
            width: 120px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: right;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.8em;
            margin-left: 5px;
        }

        .discount-input {
            width: 60%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .payment-section {
            display: none;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .payment-section input[type="text"],
        .payment-section input[type="number"] {
            width: 200px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
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

        .checkout-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .draft-btn {
            background-color: var(--dark-color);
            color: white;
            padding: 12px 15px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .draft-btn:hover {
            background-color: #23272b;
        }

        .draft-success-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 1000;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .draft-success-popup .popup-content {
            position: relative;
        }

        .draft-success-popup h3 {
            color: #28a745;
            margin-bottom: 15px;
        }

        .draft-success-popup .invoice-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .draft-success-popup .invoice-info p {
            margin: 5px 0;
        }

        .draft-success-popup .items-summary {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .draft-success-popup button {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .draft-success-popup button:hover {
            background: #1a252f;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            display: flex;
            justify-content: center; 
            align-items: center;
        }

        .draft-success-popup {
            background: white;
            border-radius: 8px;
            padding: 25px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
                max-width: 100vw;
                width: 100vw;
                padding: 0;
            }
            .product-section,
            .cart-section {
                grid-column: 1 / -1;
            }
            .customer-section {
                grid-column: 1 / -1;
            }
            .header-section {
                grid-column: 1 / -1;
            }
            .id-search-container {
                flex-direction: column;
                align-items: stretch;
            }
            #reset-customer-btn {
                width: 100%;
            }
            .payment-section input[type="text"],
            .payment-section input[type="number"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header-section" style="grid-column: 1 / -1;">
        @auth
            <div class="user-info">
                <span>
                    <i class="fas fa-user"></i>
                    User ID: {{ $user->id }}
                </span>
                
                <span id="live-datetime" style="font-weight: bold;"></span>
                
                <div class="flex flex-wrap gap-2 mt-2">
                    <a href="/pos" class="btn-nav">New Bill</a>
                    <a href="/bills" class="btn-nav">List of Bills</a>
                    <a href="/draft-bills" class="btn-nav">Draft Bills</a>
                    <a href="/credit-bills" class="btn-nav">Credit Bills</a>
                    <a href="/reports/analyze" class="btn-nav">Analyze Report</a>
                    <a href="/admin" class="btn-nav back-dashboard">← Back to Dashboard</a>
                </div>
            </div>
        @endauth
    </div>



    <div class="customer-section" style="grid-column: 1 / -1;">
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1;">
                <label for="customer-select">Customer</label>
                <select id="customer-select" onchange="selectCustomer(this.value)">
                    <option value="">-- Select a customer --</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label for="customer-phone">Search by Phone</label>
                <input type="text" id="customer-phone" placeholder="Enter phone number" onkeydown="searchCustomerByPhone(event)" />
            </div>
            <div style="flex: 1;">
                <label for="customer-id-search">Search by Customer ID</label>
                <div class="id-search-container">
                    <input type="text" id="customer-id-search" placeholder="Search by Customer ID..." onkeydown="searchCustomerById(event)" />
                    <button id="reset-customer-btn" onclick="resetCustomer()" title="Reset Customer Selection">
                        <i class="fas fa-sync-alt"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div id="customer-info" class="customer-info" style="display:none;">
            <span><strong>ID:</strong> <span id="cust-id"></span></span> &nbsp;|&nbsp;
            <span><strong>Name:</strong> <span id="cust-name"></span></span> &nbsp;|&nbsp;
            <span><strong>Phone:</strong> <span id="cust-phone"></span></span> &nbsp;|&nbsp;
            <span><strong>Remaining Balance:</strong> Rs.<span id="cust-balance"></span></span>
        </div>
    </div>

    <div class="product-section">
        <h2>Products</h2>

        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <input type="text" id="barcode-input" placeholder="Scan barcode..." 
                style="flex: 1; padding: 10px 15px; border: 1px solid #e0e0e0; border-radius: 4px;"
                onkeydown="handleBarcodeEnter(event)">
            <input type="text" id="item-code-input" placeholder="Enter item code..." 
                style="flex: 1; padding: 10px 15px; border: 1px solid #e0e0e0; border-radius: 4px;"
                onkeydown="handleItemCodeEnter(event)">
        </div>

        <input type="text" id="product-search" placeholder="Search products..." oninput="liveSearchProducts()" />

        <div class="product-grid" id="product-grid">
            <div class="loading">Loading products...</div>
        </div>
    </div>

    <!-- Item Details Popup -->
    <div id="item-popup" class="popup-container" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Item Details</h3>
                <button class="popup-close" onclick="closeItemPopup()">&times;</button>
            </div>
            <div class="popup-body">
                <div class="item-detail">
                    <span class="detail-label">Item Code:</span>
                    <span id="popup-item-code"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">ID:</span>
                    <span id="popup-item-id"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Name:</span>
                    <span id="popup-item-name"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Available Quantity:</span>
                    <span id="popup-item-quantity"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Original Price:</span>
                    <span id="popup-item-price"></span>
                </div>
                <div class="item-detail">
                    <span class="detail-label">Cart Price:</span>
                    <input type="number" id="popup-item-cart-price" class="price-input" min="0" step="0.01" 
                        onkeydown="handlePopupPriceEnter(event)" />
                </div>
                <div class="fraction-buttons">
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(0.25)">¼</button>
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(0.5)">½</button>
                    <button type="button" class="fraction-btn" onclick="setPopupQuantity(0.75)">¾</button>
                </div>
                <div class="quantity-selector">
                    <label for="popup-item-qty">Quantity:</label>
                    <div class="qty-control">
                        <button class="qty-btn" onclick="adjustPopupQuantity(-1)">-</button>
                        <input type="number" id="popup-item-qty" value="1" min="0.25" step="0.25" max="100" 
                            onkeydown="handlePopupQuantityEnter(event)" />
                        <button class="qty-btn" onclick="adjustPopupQuantity(1)">+</button>
                    </div>
                </div>
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closeItemPopup()">Cancel</button>
                <button class="popup-btn confirm" id="popup-confirm-btn" onclick="confirmAddToCart()">Add to Cart</button>
            </div>
        </div>
    </div>

    <!-- Payment Popup -->
    <div id="payment-popup" class="popup-container" style="display: none;">
        <div class="popup-content">
            <div class="popup-header">
                <h3>Process Payment</h3>
                <button class="popup-close" onclick="closePaymentPopup()">&times;</button>
            </div>
            <div class="popup-body">
                <div class="item-detail">
                    <span class="detail-label">Order Total:</span>
                    <span id="payment-total-amount">Rs.0.00</span>
                </div>
                
                <div class="item-detail">
                    <span class="detail-label">Payment Method:</span>
                    <div id="payment-method-buttons" class="payment-button-group">
                        <button type="button" class="payment-btn active" data-method="cash" onclick="selectPaymentMethod('cash')">Cash</button>
                        <button type="button" class="payment-btn" data-method="card" onclick="selectPaymentMethod('card')">Card</button>
                        <button type="button" class="payment-btn" data-method="cheque" onclick="selectPaymentMethod('cheque')">Cheque</button>
                        <button type="button" class="payment-btn" data-method="credit" id="credit-option" onclick="selectPaymentMethod('credit')">Credit</button>
                    </div>
                    <input type="hidden" id="payment-method" value="cash" />
                </div>
                
                <!-- Cash Payment Section -->
                <div id="cash-payment-section" class="payment-section">
                    <div class="item-detail">
                        <span class="detail-label">Cash Received:</span>
                        <input type="number" id="cash-received" class="price-input" min="0" step="0.01" 
                            oninput="calculateCashBalance()" />
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">Balance:</span>
                        <span id="cash-balance">Rs.0.00</span>
                    </div>
                </div>
                
                <!-- Card Payment Section -->
                <div id="card-payment-section" class="payment-section" style="display: none;">
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
                <div id="cheque-payment-section" class="payment-section" style="display: none;">
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
                <div id="credit-payment-section" class="payment-section" style="display: none;">
                    <div class="item-detail">
                        <span class="detail-label">Current Balance:</span>
                        <span id="current-credit-balance">Rs.0.00</span>
                    </div>
                    <div class="item-detail">
                        <span class="detail-label">New Balance:</span>
                        <span id="new-credit-balance">Rs.0.00</span>
                    </div>
                </div>
            </div>
            <div class="popup-footer">
                <button class="popup-btn cancel" onclick="closePaymentPopup()">Cancel</button>
                <button class="popup-btn confirm" onclick="completePayment()">Complete Payment</button>
            </div>
        </div>
    </div>

    <div class="cart-section">
        <h2>Shopping Cart</h2>
        <table id="cart-table" style="display:none;">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-body"></tbody>
        </table>
        <div id="empty-cart" class="empty-cart">
            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
            <p>Your cart is empty</p>
        </div>

        <div class="total-section">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span><span id="subtotal">Rs.0.00</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Discount:</span>
                <input type="number" id="discount-input" class="discount-input" placeholder="Enter discount amount" min="0" step="0.01" onchange="updateTotals()" />
            </div>
            <div style="display: flex; justify-content: space-between;" class="grand-total">
                <span>Total:</span><span id="grand-total">Rs.0.00</span>
            </div>
            <div class="checkout-actions">
                <button class="checkout-btn" onclick="checkout()">
                    <i class="fas fa-credit-card"></i> Process Payment
                </button>
                <button class="draft-btn" onclick="saveDraftInvoice()">
                    <i class="fas fa-save"></i> Save Draft
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ========== Enhanced POS System JavaScript ==========

    // Global variables
    let cart = [];
    let selectedCustomer = null;
    let products = [];
    let allowAutoFocus = true;

    // Fetch all customers for dropdown
    async function fetchCustomers() {
        try {
            const res = await fetch('/api/customers/search?term=');
            const customers = await res.json();
            const select = document.getElementById('customer-select');
            select.options.length = 1; // Keep default option
            customers.forEach(c => {
                const option = document.createElement('option');
                option.value = JSON.stringify(c);
                option.textContent = `${c.name} (${c.phone_1})`;
                select.appendChild(option);
            });
        } catch (e) {
            console.error('Error loading customers:', e);
        }
    }

    // Show selected customer info
    function showCustomerInfo(customer) {
        const info = document.getElementById('customer-info');
        info.style.display = 'block';
        document.getElementById('cust-id').textContent = customer.id;
        document.getElementById('cust-name').textContent = customer.name;
        document.getElementById('cust-phone').textContent = customer.phone_1;

        const balanceElem = document.getElementById('cust-balance');
        const balance = parseFloat(customer.remaining_balance ?? 0).toFixed(2);
        balanceElem.textContent = balance;

        if (balance < 0) {
            balanceElem.classList.add('negative-balance');
        } else {
            balanceElem.classList.remove('negative-balance');
        }
    }

    // Hide customer info
    function hideCustomerInfo() {
        document.getElementById('customer-info').style.display = 'none';
    }

    // Select customer from dropdown
    function selectCustomer(value) {
        if (!value) {
            selectedCustomer = null;
            hideCustomerInfo();
            return;
        }
        selectedCustomer = JSON.parse(value);
        showCustomerInfo(selectedCustomer);
    }

    // Search customer by phone on Enter key
    function searchCustomerByPhone(event) {
        if (event.key === 'Enter') {
            const phone = event.target.value.trim();
            if (!phone) return;
            fetch(`/api/customers/search?term=${encodeURIComponent(phone)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        selectedCustomer = data[0];
                        showCustomerInfo(selectedCustomer);
                        const select = document.getElementById('customer-select');
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === JSON.stringify(selectedCustomer)) {
                                select.selectedIndex = i;
                                break;
                            }
                        }
                    } else {
                        alert('Customer not found');
                        selectedCustomer = null;
                        hideCustomerInfo();
                    }
                })
                .catch(err => console.error(err));
        }
    }

    // Search customer by ID on Enter key
    function searchCustomerById(event) {
        if (event.key === 'Enter') {
            const id = document.getElementById('customer-id-search').value.trim();
            if (!id) return;

            fetch(`/api/customers/${id}`)
                .then(res => {
                    if (!res.ok) throw new Error('Customer not found');
                    return res.json();
                })
                .then(customer => {
                    selectedCustomer = customer;
                    showCustomerInfo(customer);
                    const select = document.getElementById('customer-select');
                    for (const option of select.options) {
                        if (option.value) {
                            const c = JSON.parse(option.value);
                            if (c.id == customer.id) {
                                select.value = option.value;
                                break;
                            }
                        }
                    }
                })
                .catch(() => {
                    alert('Customer not found');
                    selectedCustomer = null;
                    hideCustomerInfo();
                });
        }
    }

    // Reset customer selection
    function resetCustomer() {
        const select = document.getElementById('customer-select');
        select.selectedIndex = 0;
        document.getElementById('customer-phone').value = '';
        document.getElementById('customer-id-search').value = '';
        selectedCustomer = null;
        hideCustomerInfo();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', async () => {
        await fetchCustomers();
        await loadProducts();

        // Set focus to barcode input
        const barcodeInput = document.getElementById('barcode-input');
        barcodeInput.focus();

        // Setup event listeners
        setupEventListeners();

        // --- Item popup: click anywhere or press Enter to add to cart ---
        const itemPopup = document.getElementById('item-popup');
        const popupContent = itemPopup.querySelector('.popup-content');

        // Click anywhere on popup-content (except cancel/close/input/button) adds to cart
        popupContent.addEventListener('click', function(e) {
            if (
                e.target.closest('.popup-btn.cancel') ||
                e.target.closest('.popup-close')
            ) return;
            // Only trigger if not clicking on input or button
            if (
                e.target.tagName !== 'INPUT' &&
                e.target.tagName !== 'BUTTON' &&
                e.target.tagName !== 'SELECT'
            ) {
                confirmAddToCart();
            }
        });

        // Press Enter anywhere in popup (except in price/qty fields) adds to cart
        itemPopup.addEventListener('keydown', function(e) {
            const activeId = document.activeElement.id;
            if (
                e.key === 'Enter' &&
                activeId !== 'popup-item-cart-price' &&
                activeId !== 'popup-item-qty'
            ) {
                e.preventDefault();
                confirmAddToCart();
            }
        });
    });

    function setupEventListeners() {
        // Handle clicks to manage barcode input focus
        document.addEventListener('click', (e) => {
            const ignoreFocusSelectors = [
                '#customer-select', '#customer-phone', '#customer-id-search',
                '#item-code-input', '.product-card', '.qty-btn', '.remove-btn',
                '.checkout-btn', '.notification', '#product-search',
                '#item-popup', '.popup-content', '.popup-btn', '.popup-close', '.qty-control', '.fraction-buttons'
            ];

            // Check if click was on an element that should cancel barcode focus
            for (let selector of ignoreFocusSelectors) {
                if (e.target.closest(selector)) {
                    allowAutoFocus = false;
                    return;
                }
            }

            // Return focus to barcode input if nothing else is focused
            if (allowAutoFocus && document.activeElement !== document.getElementById('barcode-input')) {
                document.getElementById('barcode-input').focus();
            }

            allowAutoFocus = true;
        });
    }

    // ========== Product Management ==========

    async function loadProducts(searchTerm = '') {
        const productGrid = document.getElementById('product-grid');
        productGrid.innerHTML = '<div class="loading">Loading products...</div>';

        try {
            const params = new URLSearchParams();
            if (searchTerm) params.append('search', searchTerm);

            const res = await fetch(`/api/products?${params.toString()}`);
            if (!res.ok) throw new Error('Failed to fetch products');
            products = await res.json();

            renderProducts(products);
        } catch (error) {
            console.error('Error loading products:', error);
            productGrid.innerHTML = '<div class="error">Error loading products</div>';
        }
    }

    function renderProducts(products) {
        const productGrid = document.getElementById('product-grid');

        if (products.length === 0) {
            productGrid.innerHTML = `
                <div class="empty-cart" style="grid-column: 1 / -1;">
                    <i class="fas fa-box-open"></i>
                    <p>No products found</p>
                </div>`;
            return;
        }

        productGrid.innerHTML = '';
        products.forEach(product => {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.dataset.id = product.id;
            card.dataset.name = product.name;
            card.dataset.price = product.selling_price;
            card.dataset.stock = product.available_quantity;

            const imageUrl = product.image || '/images/no-image.png';
            const marketPrice = product.market_price > product.selling_price
                ? `<span style="color: #999; text-decoration: line-through; font-size: 0.9rem;">
                    Rs.${parseFloat(product.market_price).toFixed(2)}
                </span>`
                : '';

            card.innerHTML = `
                <img src="${imageUrl}" alt="${product.name}" 
                    onerror="this.src='/images/no-image.png'">
                <h3>${product.name}</h3>
                <div class="price">Rs.${parseFloat(product.selling_price).toFixed(2)}</div>
                ${marketPrice}
                <div class="stock">Stock: ${product.available_quantity}</div>
            `;

            card.addEventListener('click', () => showProductPopup(product));
            productGrid.appendChild(card);
        });
    }

    let searchTimeout;
    function liveSearchProducts() {
        clearTimeout(searchTimeout);
        const term = document.getElementById('product-search').value.trim();

        searchTimeout = setTimeout(() => {
            if (term.length === 0) {
                renderProducts(products);
            } else {
                const filtered = products.filter(p =>
                    p.name.toLowerCase().includes(term.toLowerCase()) ||
                    (p.item_code && p.item_code.includes(term))
                );
                renderProducts(filtered);
            }
        }, 300);
    }

    async function handleBarcodeEnter(event) {
        if (event.key === 'Enter') {
            const barcode = event.target.value.trim();
            if (!barcode) return;
            event.target.value = '';

            try {
                const res = await fetch(`/api/products?barcode=${barcode}`);
                if (!res.ok) throw new Error('Product not found');
                const data = await res.json();

                if (data.length > 0) {
                    showProductPopup(data[0]);
                    playSuccessSound();
                    showNotification('success', `${data[0].name} found`);
                } else {
                    playErrorSound();
                    showNotification('error', 'Product not found');
                }
            } catch (error) {
                console.error('Barcode search error:', error);
                playErrorSound();
                showNotification('error', 'Search failed');
            }
        }
    }

    async function handleItemCodeEnter(event) {
        if (event.key === 'Enter') {
            const code = event.target.value.trim();
            if (!code) return;
            event.target.value = '';

            try {
                const res = await fetch(`/api/products?item_code=${code}`);
                if (!res.ok) throw new Error('Product not found');
                const data = await res.json();

                if (data.length > 0) {
                    showProductPopup(data[0]);
                    playSuccessSound();
                    showNotification('success', `${data[0].name} found`);
                } else {
                    playErrorSound();
                    showNotification('error', 'Product not found');
                }
            } catch (error) {
                console.error('Item code search error:', error);
                playErrorSound();
                showNotification('error', 'Search failed');
            }
        }
    }

    // ========== Cart Management ==========

    function addToCart(productId, productName, price, cartPrice) {
        // Find product in cache to check stock
        const product = products.find(p => p.id === productId);
        if (!product) {
            showNotification('error', 'Product not found');
            return;
        }

        // Check if already in cart
        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            // Check stock before increasing quantity
            if (existingItem.qty >= product.available_quantity) {
                showNotification('error', 'Not enough stock available');
                return;
            }
            existingItem.qty++;
            existingItem.total = existingItem.qty * existingItem.cartPrice;
        } else {
            // Verify at least 1 in stock
            if (product.available_quantity < 1) {
                showNotification('error', 'Out of stock');
                return;
            }
            cart.push({
                id: productId,
                name: productName,
                price: parseFloat(price),
                cartPrice: parseFloat(cartPrice),
                qty: 1,
                total: parseFloat(cartPrice)
            });
        }

        renderCart();
        playSuccessSound();
        showNotification('success', `${productName} added to cart`);
    }

    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        renderCart();
        playSuccessSound();
    }

    function changeQuantity(productId, delta) {
        const item = cart.find(i => i.id === productId);
        if (!item) return;

        const product = products.find(p => p.id === productId);
        const newQty = parseFloat((item.qty + delta).toFixed(2));

        if (newQty < 0.25) {
            removeFromCart(productId);
            return;
        }

        // Check stock availability
        if (product && newQty > product.available_quantity) {
            showNotification('error', 'Not enough stock available');
            return;
        }

        item.qty = newQty;
        item.total = item.qty * item.cartPrice;
        renderCart();
    }

    function updateCartItemPrice(productId, newPrice) {
        const item = cart.find(i => i.id === productId);
        if (!item) return;
        
        item.cartPrice = parseFloat(newPrice);
        item.total = item.qty * item.cartPrice;
        renderCart();
    }

    function renderCart() {
        const cartBody = document.getElementById('cart-body');
        const cartTable = document.getElementById('cart-table');
        const emptyCart = document.getElementById('empty-cart');

        if (cart.length === 0) {
            cartTable.style.display = 'none';
            emptyCart.style.display = 'block';
            updateTotals(0);
            return;
        }

        cartTable.style.display = 'table';
        emptyCart.style.display = 'none';
        cartBody.innerHTML = '';

        cart.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.name}</td>
                <td>
                    <input type="number" 
                        class="price-input" 
                        value="${item.cartPrice.toFixed(2)}" 
                        min="0" step="1"
                        data-product-id="${item.id}"
                        onchange="updateCartItemPrice(${item.id}, this.value)" />
                    <span class="original-price">Rs.${item.price.toFixed(2)}</span>
                </td>
                <td>
                    <input type="number" 
                        class="cart-qty-input" 
                        value="${item.qty}" 
                        min="1" step="1" 
                        style="width:60px;" 
                        data-product-id="${item.id}" />
                </td>
                <td>Rs.${item.total.toFixed(2)}</td>
                <td><button class="remove-btn" onclick="removeFromCart(${item.id})">
                    <i class="fas fa-trash"></i>
                </button></td>
            `;
            cartBody.appendChild(tr);
        });

        // Add event listeners to quantity inputs
        document.querySelectorAll('.cart-qty-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const productId = parseInt(e.target.dataset.productId);
                let newQty = parseFloat(e.target.value);

                if (isNaN(newQty) || newQty < 0.25) {
                    newQty = 0.25;
                }

                const product = products.find(p => p.id === productId);
                if (product && newQty > product.available_quantity) {
                    alert('Not enough stock available');
                    e.target.value = cart.find(i => i.id === productId).qty;
                    return;
                }

                if (newQty === 0) {
                    removeFromCart(productId);
                    return;
                }

                const item = cart.find(i => i.id === productId);
                if (item) {
                    item.qty = newQty;
                    item.total = item.qty * item.cartPrice;
                    renderCart();
                }
            });

            // Optional: handle Enter key to blur input and trigger change
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.target.blur();
                }
            });
        });

        updateTotals();
    }

    function adjustPopupQuantity(delta) {
        const input = document.getElementById('popup-item-qty');
        let newVal = parseFloat(input.value) + delta;
        const min = parseFloat(input.min) || 0.25;
        const max = parseFloat(input.max) || 100;

        if (newVal < min) newVal = min;
        if (newVal > max) newVal = max;

        // Keep 2 decimals, no forced rounding to 0.25 here since user can input
        input.value = newVal.toFixed(2);
    }

    function setPopupQuantity(value) {
        const input = document.getElementById('popup-item-qty');
        const min = parseFloat(input.min) || 0.25;
        const max = parseFloat(input.max) || 100;
        let val = parseFloat(value);

        if (isNaN(val)) val = min;
        if (val < min) val = min;
        if (val > max) val = max;

        input.value = val.toFixed(2);
    }

    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
        const discountInput = document.getElementById('discount-input');
        const discount = parseFloat(discountInput.value) || 0;
        const grandTotal = subtotal - discount;

        document.getElementById('subtotal').textContent = `Rs.${subtotal.toFixed(2)}`;
        document.getElementById('grand-total').textContent = `Rs.${grandTotal.toFixed(2)}`;
    }

    // ========== Payment Processing ==========

    function checkout() {
        if (cart.length === 0) {
            showNotification('error', 'Cart is empty');
            return;
        }

        showPaymentPopup();
    }

    // Show payment popup
    function showPaymentPopup() {
        if (cart.length === 0) {
            showNotification('error', 'Cart is empty');
            return;
        }

        const popup = document.getElementById('payment-popup');
        const total = cart.reduce((sum, item) => sum + item.total, 0) - 
                     (parseFloat(document.getElementById('discount-input').value) || 0);
        
        document.getElementById('payment-total-amount').textContent = `Rs.${total.toFixed(2)}`;
        document.getElementById('cash-received').value = total.toFixed(2);
        
        // Set default to cash and focus on cash received field
        document.getElementById('payment-method').value = 'cash';
        
        // Hide/show credit option based on customer selection
        const creditOption = document.getElementById('credit-option');
        if (!selectedCustomer) {
            creditOption.style.display = 'none';
        } else {
            creditOption.style.display = 'block';
            // Show customer credit info
            const balance = parseFloat(document.getElementById('cust-balance').textContent);
            document.getElementById('current-credit-balance').textContent = `Rs.${balance.toFixed(2)}`;
            document.getElementById('new-credit-balance').textContent = `Rs.${(balance + total).toFixed(2)}`;
        }
        
        // Initialize fields
        updatePaymentFields();
        popup.style.display = 'flex';
        
        // Focus and highlight cash received field
        const cashReceivedField = document.getElementById('cash-received');
        cashReceivedField.focus();
        cashReceivedField.select();
        cashReceivedField.classList.add('highlight-field');
        
        // Remove highlight after animation completes
        setTimeout(() => {
            cashReceivedField.classList.remove('highlight-field');
        }, 2000);
    }

    function selectPaymentMethod(method) {
        document.getElementById('payment-method').value = method;
        updatePaymentFields();

        // Highlight selected button
        document.querySelectorAll('.payment-btn').forEach(btn => {
            if (btn.dataset.method === method) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // Close payment popup
    function closePaymentPopup() {
        document.getElementById('payment-popup').style.display = 'none';
        // Return focus to barcode input when popup closes
        document.getElementById('barcode-input').focus();
    }

    // Update payment fields based on selected method
    function updatePaymentFields() {
        const method = document.getElementById('payment-method').value;
        
        // Hide all sections first
        document.querySelectorAll('.payment-section').forEach(section => {
            section.style.display = 'none';
        });
        
        // Show relevant section
        document.getElementById(`${method}-payment-section`).style.display = 'block';
        
        // Special handling for cash to recalculate balance
        if (method === 'cash') {
            calculateCashBalance();
        }
    }

    // Calculate cash balance
    function calculateCashBalance() {
        const total = parseFloat(document.getElementById('payment-total-amount').textContent.replace('Rs.', ''));
        const received = parseFloat(document.getElementById('cash-received').value) || 0;
        const balance = received - total;
        
        document.getElementById('cash-balance').textContent = `Rs.${balance.toFixed(2)}`;
        
        // Highlight negative balance
        if (balance < 0) {
            document.getElementById('cash-balance').classList.add('negative-balance');
        } else {
            document.getElementById('cash-balance').classList.remove('negative-balance');
        }
    }

    // Complete payment process
    async function completePayment() {
        const method = document.getElementById('payment-method').value;
        const total = parseFloat(document.getElementById('payment-total-amount').textContent.replace('Rs.', ''));
        
        // Validate based on payment method
        let paymentData = { method };
        
        try {
            if (method === 'cash') {
                const received = parseFloat(document.getElementById('cash-received').value) || 0;
                if (received < total) {
                    throw new Error('Amount received is less than total');
                }
                paymentData.amount_received = received;
                paymentData.balance = received - total;
            }
            else if (method === 'card') {
                const reference = document.getElementById('card-reference').value.trim();
                if (!reference) {
                    throw new Error('Reference number is required');
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
                if (!selectedCustomer) {
                    throw new Error('No customer selected for credit payment');
                }
                paymentData.customer_id = selectedCustomer.id;
                paymentData.new_balance = parseFloat(document.getElementById('new-credit-balance').textContent.replace('Rs.', ''));
            }
            
            // Prepare order data
            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            
            const order = {
                customer_id: selectedCustomer ? selectedCustomer.id : null,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.qty,
                    unit_price: item.cartPrice,
                    original_price: item.price
                })),
                subtotal: cart.reduce((sum, item) => sum + item.total, 0),
                discount: discount,
                total: total,
                payment: paymentData
            };
            
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(order)
            });
            
            if (!res.ok) throw new Error('Payment failed');
            
            const result = await res.json();
            
            // Success - clear cart and show receipt
            cart = [];
            renderCart();
            document.getElementById('discount-input').value = '';
            closePaymentPopup();
            playSuccessSound();
            showNotification('success', 'Payment processed successfully');
            
            // Print receipt
            printReceipt(result.order_id, discount, paymentData);
            
        } catch (error) {
            console.error('Payment error:', error);
            showNotification('error', error.message || 'Payment failed. Please try again.');
            playErrorSound();
        }
    }

    function printReceipt(orderId, discount, paymentData) {
        // This would be implemented based on your receipt printer setup
        // Could use a dedicated receipt printing library or API

        // For demo purposes, we'll just show a popup
        const paymentMethodDetails = {
            'cash': `Cash Received: Rs.${paymentData.amount_received.toFixed(2)}\nBalance: Rs.${paymentData.balance.toFixed(2)}`,
            'card': `Card Payment\nReference: ${paymentData.reference}\nBank: ${paymentData.bank || 'N/A'}`,
            'cheque': `Cheque Payment\nCheque No: ${paymentData.cheque_no}\nBank: ${paymentData.bank}\nRemarks: ${paymentData.remarks || 'N/A'}`,
            'credit': `Credit Payment\nPrevious Balance: Rs.${(paymentData.new_balance - (document.getElementById('grand-total').textContent.replace('Rs.', ''))).toFixed(2)}\nNew Balance: Rs.${paymentData.new_balance.toFixed(2)}`
        };

        const receiptContent = `
            <h3>Order #${orderId}</h3>
            <p>Date: ${new Date().toLocaleString()}</p>
            ${selectedCustomer ? `<p>Customer: ${selectedCustomer.name} (${selectedCustomer.phone_1})</p>` : ''}
            <hr>
            ${cart.map(item => `
                <p>${item.name} - ${item.qty} x Rs.${item.cartPrice.toFixed(2)} (Orig: Rs.${item.price.toFixed(2)}) = Rs.${item.total.toFixed(2)}</p>
            `).join('')}
            <hr>
            <p>Subtotal: Rs.${(cart.reduce((sum, item) => sum + item.total, 0)).toFixed(2)}</p>
            <p>Discount: Rs.${discount.toFixed(2)}</p>
            <p><strong>Total: Rs.${document.getElementById('grand-total').textContent.replace('Rs.', '')}</strong></p>
            <hr>
            <p>Payment Method: ${paymentData.method.toUpperCase()}</p>
            <p>${paymentMethodDetails[paymentData.method]}</p>
        `;

        alert(receiptContent); // Replace with actual print functionality
    }

    // ========== Product Popup ==========

    function showProductPopup(product) {
        const popup = document.getElementById('item-popup');

        document.getElementById('popup-item-id').textContent = product.id;
        document.getElementById('popup-item-code').textContent = product.item_code || 'N/A';
        document.getElementById('popup-item-name').textContent = product.name;
        document.getElementById('popup-item-price').textContent = `Rs.${parseFloat(product.selling_price).toFixed(2)}`;
        document.getElementById('popup-item-quantity').textContent = product.available_quantity;

        const cartPriceInput = document.getElementById('popup-item-cart-price');
        cartPriceInput.value = parseFloat(product.selling_price).toFixed(2);
        cartPriceInput.focus();
        cartPriceInput.select();

        const qtyInput = document.getElementById('popup-item-qty');
        qtyInput.value = 1;
        qtyInput.min = 0.25;
        qtyInput.step = 0.25;
        qtyInput.max = product.available_quantity;

        popup.style.display = 'flex';
        popup.dataset.productId = product.id;
    }

    itemPopup.addEventListener('keydown', function(e) {
        const activeId = document.activeElement.id;
        if (
            e.key === 'Enter' &&
            activeId !== 'popup-item-cart-price' &&
            activeId !== 'popup-item-qty'
        ) {
            e.preventDefault();
            confirmAddToCart();
        }
    });

    function handlePopupPriceEnter(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            confirmAddToCart();
        }
    }

    function handlePopupQuantityEnter(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            confirmAddToCart();
        }
    }

    function closeItemPopup() {
        document.getElementById('item-popup').style.display = 'none';
        document.getElementById('barcode-input').focus(); 
    }

    function confirmAddToCart() {
        const popup = document.getElementById('item-popup');
        const productId = popup.dataset.productId;
        const quantityInput = document.getElementById('popup-item-qty');
        const cartPriceInput = document.getElementById('popup-item-cart-price');

        let quantity = parseFloat(quantityInput.value);
        let cartPrice = parseFloat(cartPriceInput.value);

        if (isNaN(quantity) || quantity <= 0) {
            showNotification('error', 'Please enter a valid quantity');
            quantityInput.focus();
            return;
        }

        if (isNaN(cartPrice) || cartPrice < 0) {
            showNotification('error', 'Please enter a valid price');
            cartPriceInput.focus();
            return;
        }

        const product = products.find(p => p.id == productId);
        if (!product) return;

        const existingItem = cart.find(item => item.id == productId);
        const totalQty = existingItem ? existingItem.qty + quantity : quantity;

        if (totalQty > product.available_quantity) {
            showNotification('error', 'Not enough stock available');
            return;
        }

        if (existingItem) {
            existingItem.qty += quantity;
            existingItem.cartPrice = cartPrice;
            existingItem.total = existingItem.qty * existingItem.cartPrice;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.selling_price),
                cartPrice: cartPrice,
                qty: quantity,
                total: cartPrice * quantity
            });
        }

        renderCart();
        closeItemPopup();
        playSuccessSound();
        showNotification('success', `${product.name} added to cart`);
    }

    // Disable Enter key globally from submitting or triggering unwanted add
    document.addEventListener('keydown', function (e) {
        const popup = document.getElementById('item-popup');
        if (popup.style.display === 'flex') {
            const activeId = document.activeElement.id;
            if (e.key === 'Enter' && activeId !== 'popup-item-cart-price' && activeId !== 'popup-item-qty') {
                e.preventDefault(); 
            }
        }
    });

    // ========== Utilities ==========

    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 500);
        }, 3000);
    }

    function playSuccessSound() {
        // Uncomment and replace with your sound file path if you want sounds
        // new Audio('/sounds/success.mp3').play().catch(e => console.log('Sound error:', e));
    }

    function playErrorSound() {
        // Uncomment and replace with your sound file path if you want sounds
        // new Audio('/sounds/error.mp3').play().catch(e => console.log('Sound error:', e));
    }


    // ========== Save Invoice as draft ==========
    async function saveDraftInvoice() {
    if (cart.length === 0) {
        showNotification('error', 'Cannot save an empty cart as draft');
        playErrorSound();
        return;
    }

    try {
        const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
        const total = subtotal - discount;

        const userId = {{ auth()->id() ?? 'null' }};

        const draftData = {
            customer_id: selectedCustomer ? selectedCustomer.id : null,
            subtotal: subtotal,
            discount: discount,
            total: total,
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.qty,
                cost_price: item.price,
                selling_price: item.cartPrice,
                line_total: item.total
            })),
            user_id: userId 
        };

        const response = await fetch('/api/draft-invoices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(draftData)
        });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to save draft invoice');
            }

            // Clear cart and inputs
            cart = [];
            renderCart();
            document.getElementById('discount-input').value = '';

            showDraftSuccessPopup(result.invoice);
            playSuccessSound();

        } catch (error) {
            console.error('Error saving draft invoice:', error);
            showNotification('error', error.message || 'Failed to save draft invoice');
            playErrorSound();
        }
    }

    // ========== Draft Success Popup ==========
    function showDraftSuccessPopup(invoice) {
        const overlay = document.createElement('div');
        overlay.className = 'popup-overlay';
        
        document.body.style.overflow = 'hidden';

        const popup = document.createElement('div');
        popup.className = 'draft-success-popup';
        popup.innerHTML = `
            <div class="popup-content">
                <h3 style="color: var(--success-color); margin-bottom: 15px;">Draft Saved Successfully!</h3>
                <div class="invoice-info" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                    <p><strong>Draft #:</strong> ${invoice.id}</p>
                    <p><strong>Date:</strong> ${new Date(invoice.created_at).toLocaleString()}</p>
                    <p><strong>Items:</strong> ${invoice.items_count}</p>
                    <p><strong>Subtotal:</strong> Rs.${invoice.subtotal.toFixed(2)}</p>
                    <p><strong>Discount:</strong> Rs.${invoice.discount.toFixed(2)}</p>
                    <p><strong>Total:</strong> Rs.${invoice.total.toFixed(2)}</p>
                </div>
                <div class="items-summary" style="max-height: 200px; overflow-y: auto; margin-bottom: 15px;">
                    <h4>Items Summary:</h4>
                    <ul style="padding-left: 20px;">
                        ${invoice.items_summary.map(item => `
                            <li style="margin-bottom: 5px;">${item.name} (${item.quantity} x Rs.${item.price.toFixed(2)}) = Rs.${item.total.toFixed(2)}</li>
                        `).join('')}
                    </ul>
                </div>
                <button onclick="closeDraftSuccessPopup()" 
                    style="background: var(--primary-color); color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: 600;">
                    Close
                </button>
            </div>
        `;

        overlay.appendChild(popup);
        document.body.appendChild(overlay);

        setTimeout(() => {
            const closeBtn = popup.querySelector('button');
            if (closeBtn) closeBtn.focus();
        }, 100);
    }

    function closeDraftSuccessPopup() {
        const overlay = document.querySelector('.popup-overlay');
        if (overlay) {
            overlay.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                overlay.remove();
                document.body.style.overflow = ''; 
                window.location.reload();
            }, 300);
        } else {
            window.location.reload();
        }
    }

    // Live date/time updater
    function updateLiveDateTime() {
        const dtElem = document.getElementById('live-datetime');
        if (dtElem) {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            dtElem.textContent = now.toLocaleString('en-US', options);
        }
    }

    // Initialize immediately and update every second
    updateLiveDateTime();
    setInterval(updateLiveDateTime, 1000);
</script>

</body>
</html>