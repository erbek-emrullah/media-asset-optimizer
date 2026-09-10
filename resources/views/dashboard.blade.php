<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Media Asset Optimizer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f2f5; color: #333; }

        .header {
            background: #1a1a2e;
            color: white;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 20px; font-weight: 600; }

        .container { max-width: 1200px; margin: 24px auto; padding: 0 16px; }

        /* upload bolumu */
        .upload-section {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .upload-section h2 { font-size: 18px; margin-bottom: 16px; }

        .upload-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .upload-form .field { flex: 1; min-width: 200px; }
        .upload-form label { display: block; font-size: 13px; color: #666; margin-bottom: 4px; }
        .upload-form input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .upload-form input[type="text"]:focus { outline: none; border-color: #7c83ff; }

        .file-btn {
            padding: 8px 16px;
            background: #f0f2f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }
        .file-btn:hover { background: #e4e6e9; }

        .submit-btn {
            padding: 8px 24px;
            background: #1a1a2e;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .submit-btn:hover { background: #16213e; }
        .submit-btn:disabled { background: #999; cursor: not-allowed; }

        #upload-status {
            margin-top: 12px;
            font-size: 14px;
            display: none;
        }
        .status-success { color: #155724; }
        .status-error { color: #721c24; }

        /* gorsel listesi */
        .images-section h2 { font-size: 18px; margin-bottom: 16px; }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .image-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card-preview {
            height: 180px;
            background: #f8f8f8;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .card-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .card-preview video {
            max-width: 100%;
            max-height: 100%;
        }
        .card-info {
            padding: 12px 16px;
            border-top: 1px solid #eee;
        }
        .card-info h3 { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
        .card-info .meta { font-size: 12px; color: #888; }

        .card-variants {
            padding: 8px 16px 12px;
            display: flex;
            gap: 6px;
        }
        .variant-badge {
            font-size: 11px;
            background: #e8e8ff;
            color: #4a4a8a;
            padding: 2px 8px;
            border-radius: 4px;
            text-decoration: none;
        }
        .variant-badge:hover { background: #d0d0ff; }

        /* sayfalama */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 24px;
            padding-bottom: 32px;
        }
        .pagination button {
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .pagination button:hover { background: #e8e8e8; }
        .pagination button.active { background: #1a1a2e; color: white; }
        .pagination button:disabled { color: #ccc; cursor: not-allowed; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #888;
            grid-column: 1 / -1;
        }

        .loading { text-align: center; padding: 40px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Media Asset Optimizer</h1>
    </div>

    <div class="container">
        <!-- Upload Bölümü -->
        <div class="upload-section">
            <h2>Görsel Yükle</h2>
            <form id="upload-form" class="upload-form">
                <div class="field">
                    <label>Görsel Adı</label>
                    <input type="text" name="name" id="input-name" required placeholder="haber-kapak-fotografi">
                </div>
                <div class="field">
                    <label>Açıklama</label>
                    <input type="text" name="description" id="input-desc" placeholder="opsiyonel">
                </div>
                <div>
                    <label>Dosya</label>
                    <label class="file-btn" id="file-label">
                        <span id="file-text">Dosya Seç</span>
                        <input type="file" name="image" id="input-file" accept="image/*,.gif" required style="display:none">
                    </label>
                </div>
                <button type="submit" class="submit-btn" id="submit-btn">Yükle</button>
            </form>
            <div id="upload-status"></div>
        </div>

        <!-- Görsel Listesi -->
        <div class="images-section">
            <h2>Yüklenen Görseller <span id="total-count" style="font-weight:400; font-size:14px; color:#888;"></span></h2>
            <div class="image-grid" id="image-grid">
                <div class="loading">Yükleniyor...</div>
            </div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <script>
        var currentPage = 1;
        var sizes = @json(array_keys($sizes));

        // sayfa acildiginda listeyi yukle
        document.addEventListener('DOMContentLoaded', function() {
            loadImages(1);
        });

        // dosya secilince ismini goster
        document.getElementById('input-file').addEventListener('change', function() {
            var fileText = document.getElementById('file-text');
            if (this.files.length > 0) {
                fileText.textContent = this.files[0].name;
            }
        });

        // form gonderimi (ajax)
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();

            var btn = document.getElementById('submit-btn');
            var status = document.getElementById('upload-status');
            var formData = new FormData();

            formData.append('name', document.getElementById('input-name').value);
            formData.append('description', document.getElementById('input-desc').value);
            formData.append('image', document.getElementById('input-file').files[0]);

            btn.disabled = true;
            btn.textContent = 'Yükleniyor...';
            status.style.display = 'none';

            fetch('/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) { return response.json().then(function(data) { return { ok: response.ok, data: data }; }); })
            .then(function(result) {
                status.style.display = 'block';
                if (result.ok) {
                    status.className = 'status-success';
                    status.textContent = '✓ ' + result.data.message;
                    // formu temizle
                    document.getElementById('upload-form').reset();
                    document.getElementById('file-text').textContent = 'Dosya Seç';
                    // listeyi guncelle
                    loadImages(1);
                } else {
                    status.className = 'status-error';
                    var errorMsg = result.data.error || result.data.message || 'Bir hata oluştu';
                    status.textContent = '✗ ' + errorMsg;
                }
            })
            .catch(function(err) {
                status.style.display = 'block';
                status.className = 'status-error';
                status.textContent = '✗ Bağlantı hatası';
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = 'Yükle';
            });
        });

        // gorselleri yukle
        function loadImages(page) {
            currentPage = page;
            var grid = document.getElementById('image-grid');
            grid.innerHTML = '<div class="loading">Yükleniyor...</div>';

            fetch('/images/json?page=' + page, {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('total-count').textContent = '(' + data.total + ')';
                renderImages(data.data);
                renderPagination(data);
            });
        }

        // gorselleri ekranda goster
        function renderImages(images) {
            var grid = document.getElementById('image-grid');

            if (images.length === 0) {
                grid.innerHTML = '<div class="empty-state">Henüz görsel yüklenmedi</div>';
                return;
            }

            var html = '';
            images.forEach(function(img) {
                var isGif = img.mime_type === 'image/gif';

                var paddedId = String(img.id).padStart(6, '0');
                var shardPath = paddedId.substring(0, 3) + '/' + paddedId.substring(3, 6);
                var variantBase = '/storage/variants/' + shardPath + '/';

                html += '<div class="image-card">';
                html += '<div class="card-preview">';

                if (isGif) {
                    html += '<video autoplay loop muted playsinline>';
                    html += '<source src="' + variantBase + 'video.mp4" type="video/mp4">';
                    html += '</video>';
                } else {
                    html += '<img src="' + variantBase + '640xauto.jpg" alt="' + img.name + '" loading="lazy">';
                }

                html += '</div>';
                html += '<div class="card-info">';
                html += '<h3>' + img.name + '</h3>';
                html += '<span class="meta">' + img.mime_type + ' · ' + formatDate(img.created_at) + '</span>';
                html += '</div>';

                html += '<div class="card-variants">';
                if (isGif) {
                    html += '<a href="' + variantBase + 'video.mp4" target="_blank" class="variant-badge">MP4</a>';
                } else {
                    sizes.forEach(function(size) {
                        html += '<a href="' + variantBase + size + '.jpg" target="_blank" class="variant-badge">' + size + '</a>';
                    });
                }
                html += '</div>';
                html += '</div>';
            });

            grid.innerHTML = html;
        }

        // sayfalama butonlari
        function renderPagination(data) {
            var div = document.getElementById('pagination');
            if (data.last_page <= 1) { div.innerHTML = ''; return; }

            var html = '';
            html += '<button ' + (data.current_page === 1 ? 'disabled' : 'onclick="loadImages(' + (data.current_page - 1) + ')"') + '>&laquo; Önceki</button>';

            for (var i = 1; i <= data.last_page; i++) {
                html += '<button class="' + (i === data.current_page ? 'active' : '') + '" onclick="loadImages(' + i + ')">' + i + '</button>';
            }

            html += '<button ' + (data.current_page === data.last_page ? 'disabled' : 'onclick="loadImages(' + (data.current_page + 1) + ')"') + '>Sonraki &raquo;</button>';
            div.innerHTML = html;
        }

        function formatDate(dateStr) {
            var d = new Date(dateStr);
            return d.toLocaleDateString('tr-TR') + ' ' + d.toLocaleTimeString('tr-TR', {hour:'2-digit', minute:'2-digit'});
        }
    </script>
</body>
</html>