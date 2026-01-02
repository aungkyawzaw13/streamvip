function showAlert(title) {
    Swal.fire({
        title: `<span style="font-family: 'Pyidaungsu'">${title}</span>`,
        text: "ပြင်ဆင်ရန် စာမျက်နှာသို့ သွားလိုပါသလား?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2c2c34',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'သွားမည်',
        cancelButtonText: 'ပယ်ဖျက်'
    });
}

function logout() {
    Swal.fire({
        title: 'Logout',
        text: "အကောင့်ထဲမှ ထွက်ရန် သေချာပါသလား?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ထွက်မည်',
        cancelButtonText: 'မထွက်တော့ပါ'
    }).then((result) => {
        if (result.isConfirmed) {
            // Logout Logic ဒီမှာရေးပါ
            window.location.href = "login.php"; 
        }
    });
}