<!DOCTYPE html>
<html>
<head>
    <title>Debug Frontend API</title>
</head>
<body>
    <h1>Debug Frontend API Calls</h1>
    <button onclick="testAPI()">Test API Order Creation</button>
    <div id="result"></div>

    <script>
        async function testAPI() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = 'Testing...';
            
            try {
                const testData = {
                    fullName: 'Debug Test User',
                    whatsApp: '081234567890',
                    ktpId: 'DEBUG123',
                    rentalDate: '2025-12-20',
                    notes: 'Test via debug page',
                    items: [
                        {productId: 'kamera-mirrorless-1', quantity: 1, duration: 2}
                    ]
                };
                
                console.log('Sending data:', testData);
                
                const response = await fetch('api/orders.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(testData)
                });
                
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('Response data:', result);
                
                resultDiv.innerHTML = `
                    <h3>Result:</h3>
                    <pre>${JSON.stringify(result, null, 2)}</pre>
                    <p>Order ID: ${result.order_id}</p>
                `;
                
                // Check if order was actually created
                if (result.success && result.order_id) {
                    const checkResponse = await fetch(`api/orders.php?action=get_by_id&id=${result.order_id}`);
                    const orderData = await checkResponse.json();
                    console.log('Order check:', orderData);
                    
                    resultDiv.innerHTML += `
                        <h3>Order Verification:</h3>
                        <pre>${JSON.stringify(orderData, null, 2)}</pre>
                    `;
                }
                
            } catch (error) {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <h3>Error:</h3>
                    <pre>${error.message}</pre>
                `;
            }
        }
    </script>
</body>
</html>
