<style>
    /* 6. Bottom Navigation Bar */
        .bottom-tab {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 480px;
            background: #000;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            border-top: 1px solid #1a1a1a;
            z-index: 1000;
        }

        .tab-item {
            text-align: center;
            color: #666;
            text-decoration: none;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 11px;
            transition: 0.3s;
        }

        /* လက်ရှိ color ကို Active ဖြစ်ရင် ပြောင်းမယ့်အရောင် */
.tab-item.active { 
    color: #8a2be2 !important; /* ခရမ်းရောင် (သို့မဟုတ် သင်နှစ်သက်ရာ) */
}

/* Icon (SVG) အတွက်ပါ active အရောင်သက်ရောက်စေရန် */
.tab-item.active svg {
    stroke: #8a2be2; 
}

</style>
<nav class="bottom-tab">
            <a href="/home" class="tab-item active">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <p>ပင်မစာမျက်နှာ</p>
            </a>
            <a href="#" class="tab-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                <p>လုပ်ငန်း</p>
            </a>
            <a href="./wallet" class="tab-item">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
  <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
  <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
  <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path>
</svg>    
            <p>ပိုက်ဆံအိတ်</p>
            </a>
            <a href="#" class="tab-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                <p>အကျိုးအမြတ်</p>
            </a>
            <a href="./profile" class="tab-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <p>ကျွန်ုပ်</p>
            </a>
        </nav>

        <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.tab-item');
        const currentUrl = window.location.href;

        tabs.forEach(tab => {
            // ၁။ Page အသစ်ရောက်သွားရင် URL နဲ့ တိုက်စစ်ပြီး Active Class ထည့်ပေးခြင်း
            if (currentUrl.includes(tab.getAttribute('href')) && tab.getAttribute('href') !== "#") {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            }

            // ၂။ Click နှိပ်တဲ့အခါ အရောင်ချက်ချင်းပြောင်းခြင်း
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>