<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Device Status API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        button.secondary {
            background-color: #6c757d;
        }
        button.secondary:hover {
            background-color: #545b62;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #007bff;
        }
        .result h3 {
            margin-top: 0;
            color: #333;
        }
        pre {
            background-color: #282c34;
            color: #abb2bf;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.5;
        }
        .error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }
        .success {
            border-left-color: #28a745;
            background-color: #d4edda;
        }
        .info {
            margin-top: 20px;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .endpoint-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        .endpoint-info code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Device Status API</h1>
        
        <form id="testForm" onsubmit="testEndpoint(event)">
            <div class="form-group">
                <label for="device_id">Device ID *</label>
                <input type="number" id="device_id" name="device_id" required placeholder="Enter device ID (e.g., 1, 2, 3)">
            </div>
            
            <div class="form-group">
                <label for="endpoint_type">Endpoint Type</label>
                <select id="endpoint_type" name="endpoint_type">
                    <option value="list">Get All Statuses (with pagination)</option>
                    <option value="latest">Get Latest Status Only</option>
                </select>
            </div>
            
            <div id="paginationOptions" class="form-group">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label for="limit">Limit</label>
                        <input type="number" id="limit" name="limit" value="100" min="1" max="1000">
                    </div>
                    <div>
                        <label for="offset">Offset</label>
                        <input type="number" id="offset" name="offset" value="0" min="0">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                    <div>
                        <label for="order_by">Order By</label>
                        <select id="order_by" name="order_by">
                            <option value="timestamp">Timestamp</option>
                            <option value="created_at">Created At</option>
                        </select>
                    </div>
                    <div>
                        <label for="order_direction">Order Direction</label>
                        <select id="order_direction" name="order_direction">
                            <option value="desc">Descending</option>
                            <option value="asc">Ascending</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div>
                <button type="submit">Test Endpoint</button>
                <button type="button" class="secondary" onclick="testApiEndpoint()">Test API Endpoint (JSON)</button>
            </div>
        </form>
        
        <div id="result"></div>
        
        <div class="endpoint-info">
            <h3>📡 API Endpoints</h3>
            <p><strong>Web Routes (for testing):</strong></p>
            <ul>
                <li><code>/test/device-status/{device_id}</code> - Get all statuses</li>
                <li><code>/test/device-status/{device_id}/latest</code> - Get latest status</li>
            </ul>
            <p><strong>API Routes:</strong></p>
            <ul>
                <li><code>/api/device-statuses/{device_id}</code> - Get all statuses (JSON)</li>
                <li><code>/api/device-statuses/{device_id}/latest</code> - Get latest status (JSON)</li>
            </ul>
        </div>
    </div>

    <script>
        // Toggle pagination options based on endpoint type
        document.getElementById('endpoint_type').addEventListener('change', function() {
            const paginationDiv = document.getElementById('paginationOptions');
            if (this.value === 'latest') {
                paginationDiv.style.display = 'none';
            } else {
                paginationDiv.style.display = 'block';
            }
        });

        function testEndpoint(event) {
            event.preventDefault();
            const deviceId = document.getElementById('device_id').value;
            const endpointType = document.getElementById('endpoint_type').value;
            const resultDiv = document.getElementById('result');
            
            let url = `/test/device-status/${deviceId}`;
            
            if (endpointType === 'latest') {
                url += '/latest';
            } else {
                const limit = document.getElementById('limit').value;
                const offset = document.getElementById('offset').value;
                const orderBy = document.getElementById('order_by').value;
                const orderDirection = document.getElementById('order_direction').value;
                
                const params = new URLSearchParams({
                    limit: limit,
                    offset: offset,
                    order_by: orderBy,
                    order_direction: orderDirection
                });
                url += '?' + params.toString();
            }
            
            resultDiv.innerHTML = '<div class="result"><h3>Loading...</h3></div>';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    resultDiv.innerHTML = `
                        <div class="result success">
                            <h3>✅ Success</h3>
                            <p><strong>URL:</strong> <code>${url}</code></p>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                })
                .catch(error => {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <h3>❌ Error</h3>
                            <p><strong>URL:</strong> <code>${url}</code></p>
                            <pre>${error.message}</pre>
                        </div>
                    `;
                });
        }
        
        function testApiEndpoint() {
            const deviceId = document.getElementById('device_id').value;
            const endpointType = document.getElementById('endpoint_type').value;
            const resultDiv = document.getElementById('result');
            
            let url = `/api/device-statuses/${deviceId}`;
            
            if (endpointType === 'latest') {
                url += '/latest';
            } else {
                const limit = document.getElementById('limit').value;
                const offset = document.getElementById('offset').value;
                const orderBy = document.getElementById('order_by').value;
                const orderDirection = document.getElementById('order_direction').value;
                
                const params = new URLSearchParams({
                    limit: limit,
                    offset: offset,
                    order_by: orderBy,
                    order_direction: orderDirection
                });
                url += '?' + params.toString();
            }
            
            resultDiv.innerHTML = '<div class="result"><h3>Loading...</h3></div>';
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    resultDiv.innerHTML = `
                        <div class="result success">
                            <h3>✅ API Response (JSON)</h3>
                            <p><strong>URL:</strong> <code>${url}</code></p>
                            <pre>${JSON.stringify(data, null, 2)}</pre>
                        </div>
                    `;
                })
                .catch(error => {
                    resultDiv.innerHTML = `
                        <div class="result error">
                            <h3>❌ Error</h3>
                            <p><strong>URL:</strong> <code>${url}</code></p>
                            <pre>${JSON.stringify(error, null, 2)}</pre>
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>

