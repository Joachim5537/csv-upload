<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload CSV</title>
    <script>

            let sort = 'created_at';
            let order = 'asc';

        document.addEventListener('DOMContentLoaded', function() {

            const timeThead = document.getElementById('time-thead');
            const filenameThead = document.getElementById('filename-thead');
            const uploadBtn = document.getElementById('upload-btn');

            timeThead.addEventListener('click', sortTime);
            filenameThead.addEventListener('click', sortFilename);

            function loadUploads() {
                fetch("{{ route('csv.status') }}?sort="+sort+"&order="+order)
                    .then(res => res.json())
                    .then(result => {
                        const table_body = document.getElementById('upload-table');
                        table_body.innerHTML = ''; // clear table
                        result.data.forEach(upload => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="border px-4 py-2">
                                    ${upload.created_at}<br>
                                    <small class="text-gray-500">(${upload.time_ago})</small>
                                </td>
                                <td class="border px-4 py-2">${upload.file_name}</td>
                                <td class="border px-4 py-2" id="status-${upload.id}">${upload.status}</td>
                            `;
                            table_body.prepend(row);
                        });
                    })
                    .catch(err => console.error('Error loading uploads:', err));
            }

            loadUploads();
            setInterval(loadUploads, 3000);



            const dropZone = document.getElementById('drop-zone');
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('bg-gray-100');
            });
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('bg-gray-100'));
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('bg-gray-100');
                const fileInput = document.getElementById('file-input');
                fileInput.files = e.dataTransfer.files;
            });

            
        
            function sortTime(){

 
                sort = 'created_at';

                // if(order )
                if(order == 'desc'){
                    timeThead.textContent = 'Time ↑';
                    filenameThead.textContent = 'File Name ';
                    order = 'asc';
                }else{
                    timeThead.textContent = 'Time ↓';
                    filenameThead.textContent = 'File Name ';
                    order = 'desc'
                }

                loadUploads();
            }

            function sortFilename(){

                
                sort = 'created_at';
                
                if(order == 'desc'){
                    filenameThead.textContent = 'File Name ↑';
                    timeThead.textContent = 'Time ';
                    order = 'asc';
                }else{
                    filenameThead.textContent = 'File Name ↓';
                    timeThead.textContent = 'Time ';
                    order = 'desc'
                }

                loadUploads();
            }

            document.getElementById('upload-form').addEventListener('submit', function(e){
                const fileInput = document.getElementById('file-input');
                if (!fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Please upload something');
                }

                uploadBtn.disabled = true;
                uploadBtn.textContent = 'Uploading...';
                fileInput.style.pointerEvents = 'none';
                dropZone.style.pointerEvents = 'none';
                dropZone.style.opacity = '0.6';
            });

        });


    </script>
    <style>
        body {
            font-family: sans-serif;
            background-color: #fafafa;
        }
        .container {
            width: 700px;
            margin: 50px auto;
        }
        #drop-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            color: #666;
            cursor: pointer;
            transition: background 0.3s;
        }
        #drop-zone.bg-gray-100 {
            background-color: #f1f1f1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #f9f9f9;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            position: relative;
        }
        button:disabled::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            width: 14px;
            height: 14px;
            border: 2px solid white;
            border-top-color: transparent;
            border-radius: 50%;
            transform: translateY(-50%);
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        .alert {
            position: relative;
            margin: 20px 0;
            padding: 2px 18px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: sans-serif;
            font-size: 15px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert-error {
            background-color: #fdecea;
            color: #b71c1c;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #1b5e20;
        }

        .alert-close {
            background: none;
            border: none;
            font-size: 20px;
            color: #555;
            cursor: pointer;
            line-height: 1;
        }

        .alert-close:hover {
            color: black;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="container">

        @if (session('error'))
            <div class="alert alert-error" id="alert-box">
                <span class="alert-message">{{ session('error') }}</span>
                <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" id="alert-box">
                <span class="alert-message">{{ session('success') }}</span>
                <button class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
        @endif

 
        <h2 style="margin-bottom: 15px;">Upload CSV</h2>


        <form id="upload-form" action="{{ route('csv.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="drop-zone">
                Select file or drag and drop here
                <br><br>
                <input type="file" name="file" accept=".csv" required id="file-input">
            </div>
            <br>
            <button type="submit" id="upload-btn" style="width:30%">Upload File</button>            
        </form>

        <table>
            <thead>
                <tr  style="text-align:left">
                    <th id="time-thead">Time ↓</th>
                    <th id="filename-thead">File Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="upload-table"></tbody>
        </table>
    </div>
</body>
</html>
