function buyVIP(level) {
    Swal.fire({
        title: level + ' ဝယ်ယူမည်',
        text: "ဤအဆင့်ကို ဝယ်ယူရန် သေချာပါသလား?",
        icon: 'question',
        background: '#24242b',
        color: '#fff',
        showCancelButton: true,
        confirmButtonColor: '#ffcc00',
        confirmButtonText: '<span style="color:#000">ဝယ်မည်</span>',
        cancelButtonText: 'ပယ်ဖျက်'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'အောင်မြင်သည်',
                text: level + ' ကို ဝယ်ယူပြီးပါပြီ။',
                icon: 'success',
                background: '#24242b',
                color: '#fff'
            });
        }
    });
}