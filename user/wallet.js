// စာမျက်နှာစဖွင့်ချိန်မှာ Recharge History ကို အလိုအလျောက်ပြရန်
document.addEventListener('DOMContentLoaded', () => {
    const firstTab = document.querySelector('.tab');
    if (firstTab) {
        switchTab(firstTab, 'recharge');
    }
});

function switchTab(element, type) {
    // ၁။ Tab အရောင်ပြောင်းလဲခြင်း
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    element.classList.add('active');

    // ၂။ Loading ပြရန်
    const historyContainer = document.getElementById('history-list');
    historyContainer.innerHTML = '<div class="empty-state">ဒေတာများကို ရယူနေပါသည်...</div>';

    // ၃။ PHP ဖိုင်များဆီမှ Data လှမ်းခေါ်ခြင်း
    const url = (type === 'recharge') ? '/sp/user/recharge_history_data.php' : '/sp/user/withdraw_history_data.php';

    fetch(url)
        .then(response => response.json())
        .then(data => {
            renderHistory(data);
        })
        .catch(error => {
            console.error('Error:', error);
            historyContainer.innerHTML = '<div class="empty-state">ဒေတာရယူရာတွင် အမှားအယွင်းရှိနေပါသည်</div>';
        });
}

function renderHistory(data) {
    const historyContainer = document.getElementById('history-list');

    // ဒေတာမရှိလျှင်
    if (!data || data.length === 0) {
        historyContainer.innerHTML = '<div id="no-data" class="empty-state">ဒေတာမရှိပါ</div>';
        return;
    }

    // ဒေတာရှိလျှင် HTML အဖြစ်ပြောင်းလဲခြင်း
    let html = '<div class="history-list">';
    data.forEach(item => {
        // Status ပေါ်မူတည်ပြီး အရောင်ခွဲရန်
        let statusColor = '';
        if (item.status === 'success' || item.status === 'completed') statusColor = 'style="color: #28a745;"';
        else if (item.status === 'pending') statusColor = 'style="color: #ffc107;"';
        else statusColor = 'style="color: #dc3545;"';

        html += `
            <div class="history-item" style="border-bottom: 1px solid #333; padding: 10px 0; display: flex; justify-content: space-between; align-items: center;">
                <div class="info">
                    <div style="font-weight: bold; color: #fff;">${item.description}</div>
                    <div style="font-size: 12px; color: #aaa;">${item.created_at}</div>
                </div>
                <div class="amount-status" style="text-align: right;">
                    <div class="yellow-text" style="font-weight: bold;">${item.amount} ကျပ်</div>
                    <div class="status" ${statusColor} font-size: 12px;">${item.status}</div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    historyContainer.innerHTML = html;
}

// ခလုတ်နှိပ်ခြင်းများအတွက် function
function handleAction(action) {
    if (action === 'Deposit') {
        window.location.href = './recharge.php';
    }
}