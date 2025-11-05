<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - {{ $product['name'] }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body { background: #f7f8fb; }
    .card { border: none; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,0.06); }
    .qr { width: 260px; height: 260px; object-fit: contain; }
    .small-muted { font-size: 0.9rem; color: #666; }
  </style>
</head>
<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">

                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary mb-4">
                    ⬅️ Back to All Products
                </a>

                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">✨ {{ $product['name'] }}</h1>
                    </div>
                    <div class="card-body">
                        
                        <dl class="row mb-0">
                            <dt class="col-sm-3">ID:</dt>
                            <dd class="col-sm-9 text-muted">\#{{ $product['id'] }}</dd>

                            <dt class="col-sm-3">Price:</dt>
                            <dd class="col-sm-9 fs-4 fw-bold text-success">Rs. {{ number_format($product['price'], 2) }}</dd>

                            <dt class="col-sm-3">Description:</dt>
                            <dd class="col-sm-9 text-break">
                                {{ $product['description'] ?? 'No detailed description available for this product.' }}
                            </dd>

                            <dt class="col-sm-3">Status:</dt>
                            <dd class="col-sm-9">
                                <span class="badge bg-success">In Stock</span>
                            </dd>
                        </dl>
                    </div>
                    <div class="card-footer text-end">
                        <button id="createBtn" class="btn btn-warning">Pay</button>
                    </div>
                </div>
                <div id="loader" class="text-center mt-3" style="display:none;">
            <div class="spinner-border" role="status"></div>
            <div class="small-muted mt-2">Processing... please wait</div>
          </div>

          <div id="error" class="text-danger mt-3"></div>

          <div id="details" class="mt-3" style="display:none;">
            <div class="text-center">
              <h6>Status: <span id="status" class="badge bg-warning">Pending</span></h6>
              <div class="mt-3">
                <img id="qrImage" class="qr" alt="QR code will appear here" />
              </div>
              <p class="mt-3 small-muted">Scan the QR with any UPI app or click the UPI link below.</p>
              <p><a id="upiLink" href="#" target="_blank" class="d-block text-truncate" style="max-width:100%"></a></p>

              <div class="d-flex gap-2 justify-content-center mt-2">
                <button id="checkBtn" class="btn btn-outline-secondary">Check status</button>
                <button id="copyBtn" class="btn btn-outline-secondary">Copy UPI link</button>
              </div>
            </div>
          </div>
<!-- 
          <div class="mt-3 small-muted text-center">
            This is a demo. Newly created transactions will return <strong>Pending</strong> by default.
          </div>
 -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
  // Grab CSRF token from meta
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const createBtn = document.getElementById('createBtn');
  const loader = document.getElementById('loader');
  const errorBox = document.getElementById('error');
  const detailsBox = document.getElementById('details');
  const qrImage = document.getElementById('qrImage');
  const upiLinkEl = document.getElementById('upiLink');
  const statusBadge = document.getElementById('status');
  const checkBtn = document.getElementById('checkBtn');
  const copyBtn = document.getElementById('copyBtn');

  let currentToken = null;

  function showLoader(show) {
    loader.style.display = show ? 'block' : 'none';
  }

  function setError(msg) {
    errorBox.innerText = msg || '';
  }

  function setStatus(s) {
    statusBadge.innerText = s;
    statusBadge.className = 'badge ' + (s === 'Pending' ? 'bg-warning' : s === 'Completed' ? 'bg-success' : 'bg-danger');
  }

  createBtn.addEventListener('click', async () => {
    setError('');
    detailsBox.style.display = 'none';
    showLoader(true);
    try {
      // Create transaction
      const res = await fetch('/create-transaction', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
      });

      const data = await res.json();
      showLoader(false);

      if (!data || !data.status) {
        setError(data?.error_message || 'Unable to create transaction.');
        return;
      }

      // token might be number or string, accept both
      const token = data?.data?.token ?? data?.token;
      if (!token) {
        setError('No token returned by create-transaction.');
        return;
      }
      currentToken = token;

      // Get deposit details
      showLoader(true);
      const detRes = await fetch('/get-deposit-details', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ token })
      });
      const details = await detRes.json();
      showLoader(false);

      if (!details || !details.status) {
        setError(details?.error_message || 'Unable to fetch deposit details.');
        return;
      }

      // show details
      detailsBox.style.display = 'block';
      setStatus('Pending');

      // QR handling: API returns data.qr either as base64 data URL or URL
      //  QR handling: handle both base64 and URL
        let qr = details.data?.qr ?? '';
        if (qr && !qr.startsWith('data:image')) {
          qr = 'data:image/png;base64,' + qr;
        }
        qrImage.src = qr || '';
        upiLinkEl.href = details.data?.link ?? '#';
        upiLinkEl.innerText = details.data?.link ?? '';


      // attach check handler
      checkBtn.onclick = async () => {
        setError('');
        showLoader(true);
        try {
          const checkRes = await fetch('/validate-transaction', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrf,
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ token: currentToken })
          });
          const check = await checkRes.json();
          showLoader(false);
          if (!check || !check.status) {
            setError(check?.error_message || 'Unable to validate transaction.');
            return;
          }

          setStatus(check.transaction_status ?? 'Pending');
        } catch (err) {
          showLoader(false);
          setError('Error checking status: ' + err.message);
        }
      };

      copyBtn.onclick = async () => {
        const link = details.data?.link ?? '';
        if (!link) {
          setError('No UPI link to copy.');
          return;
        }
        try {
          await navigator.clipboard.writeText(link);
          copyBtn.innerText = 'Copied!';
          setTimeout(() => copyBtn.innerText = 'Copy UPI link', 1500);
        } catch (e) {
          setError('Clipboard not available.');
        }
      };

    } catch (err) {
      showLoader(false);
      setError('Something went wrong: ' + err.message);
    }
  });
</script>
</body>
</html>