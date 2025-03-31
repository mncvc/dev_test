
<div style="margin: 0 auto">
<h2>Google Authenticator로 스캔하세요:</h2>

{{ \QrCode::size(200)->generate($qrCodeUrl) }}
    <p/>
    <input  type="button" value="확인" onclick="moveClose()">
</div>

<script language="javascript">
    function moveClose() {
        opener.location.href="otpAuth";
        self.close();
    }
</script>