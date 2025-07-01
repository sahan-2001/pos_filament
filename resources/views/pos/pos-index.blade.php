<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Luxury POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        
        header {
            grid-column: 1 / -1;
            background-color: var(--primary-color);
            color: white;
            padding: 20px 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .cart-section {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--secondary-color);
        }
        
        .product-card img {
            max-width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
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
        
        .add-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .add-btn:hover {
            background-color: #9c7560;
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
        
        .remove-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .remove-btn:hover {
            background-color: #c0392b;
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
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 20px;
            width: 100%;
        }
        
        .checkout-btn:hover {
            background-color: #218838;
        }
        
        .empty-cart {
            text-align: center;
            padding: 30px;
            color: #777;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .qty-btn {
            background-color: #e0e0e0;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .qty-btn:hover {
            background-color: #d0d0d0;
        }
        
        .search-bar {
            margin-bottom: 20px;
        }
        
        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
        }
        
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="fas fa-cash-register"></i> Luxury POS System</h1>
    </header>
    
    <div class="container">
        <div class="product-section">
            <h2>Products</h2>
            
            <div class="search-bar">
                <input type="text" id="product-search" placeholder="Search products..." onkeyup="searchProducts()">
            </div>
            
            <div class="product-grid" id="product-grid">
                <?php
                // Sample product data - in a real app this would come from a database
                $products = [
                    ['id' => 1, 'name' => 'Premium Wine', 'price' => 89.99, 'image' => 'wine.jpg'],
                    ['id' => 2, 'name' => 'Artisan Cheese', 'price' => 24.95, 'image' => 'cheese.jpg'],
                    ['id' => 3, 'name' => 'Truffle Oil', 'price' => 39.99, 'image' => 'truffle.jpg'],
                    ['id' => 4, 'name' => 'Caviar', 'price' => 199.99, 'image' => 'caviar.jpg'],
                    ['id' => 5, 'name' => 'Chocolate Truffles', 'price' => 49.95, 'image' => 'chocolate.jpg'],
                    ['id' => 6, 'name' => 'Champagne', 'price' => 129.99, 'image' => 'champagne.jpg'],
                    ['id' => 7, 'name' => 'Foie Gras', 'price' => 79.99, 'image' => 'foiegras.jpg'],
                    ['id' => 8, 'name' => 'Lobster', 'price' => 59.99, 'image' => 'lobster.jpg']
                ];
                
                foreach ($products as $product) {
                    echo '
                    <div class="product-card" data-name="'.strtolower($product['name']).'">
                        <img src="assets/'.$product['image'].'" alt="'.$product['name'].'">
                        <h3>'.$product['name'].'</h3>
                        <div class="price">$'.number_format($product['price'], 2).'</div>
                        <button class="add-btn" onclick="addToCart('.$product['id'].', \''.$product['name'].'\', '.$product['price'].')">
                            <i class="fas fa-plus"></i> Add to Cart
                        </button>
                    </div>';
                }
                ?>
            </div>
        </div>
        
        <div class="cart-section">
            <h2>Shopping Cart</h2>
            
            <div class="cart-table-container">
                <table id="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <!-- Cart items will be added here dynamically -->
                    </tbody>
                </table>
                
                <div id="empty-cart" class="empty-cart">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                    <p>Your cart is empty</p>
                </div>
            </div>
            
            <div class="total-section">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal:</span>
                    <span id="subtotal">$0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Tax (10%):</span>
                    <span id="tax">$0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.2rem;">
                    <span class="grand-total">Total:</span>
                    <span class="grand-total" id="grand-total">$0.00</span>
                </div>
                
                <button class="checkout-btn" onclick="checkout()">
                    <i class="fas fa-credit-card"></i> Process Payment
                </button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        
        function searchProducts() {
            const searchTerm = document.getElementById('product-search').value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const productName = card.getAttribute('data-name');
                if (productName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function addToCart(id, name, price) {
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.qty++;
                existingItem.total = existingItem.qty * existingItem.price;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1,
                    total: price
                });
            }
            
            renderCart();
        }
        
        function updateQuantity(index, change) {
            const item = cart[index];
            item.qty += change;
            
            if (item.qty < 1) {
                cart.splice(index, 1);
            } else {
                item.total = item.qty * item.price;
            }
            
            renderCart();
        }
        
        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }
        
        function renderCart() {
            const cartBody = document.getElementById('cart-body');
            const emptyCart = document.getElementById('empty-cart');
            const subtotalEl = document.getElementById('subtotal');
            const taxEl = document.getElementById('tax');
            const grandTotalEl = document.getElementById('grand-total');
            
            cartBody.innerHTML = '';
            
            if (cart.length === 0) {
                emptyCart.style.display = 'block';
                document.getElementById('cart-table').style.display = 'none';
            } else {
                emptyCart.style.display = 'none';
                document.getElementById('cart-table').style.display = 'table';
                
                let subtotal = 0;
                
                cart.forEach((item, index) => {
                    subtotal += item.total;
                    
                    cartBody.innerHTML += `
                        <tr>
                            <td>${item.name}</td>
                            <td>$${item.price.toFixed(2)}</td>
                            <td>
                                <div class="quantity-control">
                                    <button class="qty-btn" onclick="updateQuantity(${index}, -1)">-</button>
                                    <span>${item.qty}</span>
                                    <button class="qty-btn" onclick="updateQuantity(${index}, 1)">+</button>
                                </div>
                            </td>
                            <td>$${item.total.toFixed(2)}</td>
                            <td><button class="remove-btn" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `;
                });
                
                const tax = subtotal * 0.10;
                const grandTotal = subtotal + tax;
                
                subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
                taxEl.textContent = `$${tax.toFixed(2)}`;
                grandTotalEl.textContent = `$${grandTotal.toFixed(2)}`;
            }
        }
        
        function checkout() {
            if (cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            fetch('/api/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    cart: cart,
                    subtotal: parseFloat(document.getElementById('subtotal').textContent.replace('$', '')),
                    tax: parseFloat(document.getElementById('tax').textContent.replace('$', '')),
                    total: parseFloat(document.getElementById('grand-total').textContent.replace('$', ''))
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment processed successfully!');
                    cart = [];
                    renderCart();
                    
                    // Print receipt (simulated)
                    printReceipt(data.transactionId);
                } else {
                    alert('Payment failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during checkout');
            });
        }
        
        function printReceipt(transactionId) {
            // In a real application, this would open a print dialog with formatted receipt
            const receiptWindow = window.open('', 'Receipt', 'width=600,height=800');
            
            receiptWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Receipt #${transactionId}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .receipt-header { text-align: center; margin-bottom: 20px; }
                        .receipt-header h1 { margin: 0; }
                        .receipt-details { margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                        .total-line { font-weight: bold; }
                        .thank-you { text-align: center; margin-top: 30px; font-style: italic; }
                    </style>
                </head>
                <body>
                    <div class="receipt-header">
                        <h1>LUXURY GOODS</h1>
                        <p>123 Premium Avenue<br>Beverly Hills, CA 90210</p>
                    </div>
                    
                    <div class="receipt-details">
                        <p><strong>Receipt #:</strong> ${transactionId}</p>
                        <p><strong>Date:</strong> ${new Date().toLocaleString()}</p>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cart.map(item => `
                                <tr>
                                    <td>${item.name}</td>
                                    <td>$${item.price.toFixed(2)}</td>
                                    <td>${item.qty}</td>
                                    <td>$${item.total.toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    
                    <div style="text-align: right;">
                        <p>Subtotal: $${document.getElementById('subtotal').textContent}</p>
                        <p>Tax: $${document.getElementById('tax').textContent}</p>
                        <p class="total-line">Total: $${document.getElementById('grand-total').textContent}</p>
                    </div>
                    
                    <div class="thank-you">
                        <p>Thank you for your purchase!</p>
                        <p>Please visit us again</p>
                    </div>
                </body>
                </html>
            `);
            
            receiptWindow.document.close();
            setTimeout(() => {
                receiptWindow.print();
                receiptWindow.close();
            }, 500);
        }
    </script>
</body>
</html>