requirejs(['jquery','mage/url',], function($,mageUrl1,){
    /* if($("#switch").prop("checked") == true){
        $("#textstatus").text("Excl. BTW");
    }else{
        $("#textstatus").text("Incl. BTW");
    } */
    $("#switch").click(function(){
        var AjaxUrl = mageUrl1.build('vatexempt/doc/changestatus');
        var status = 0;
        if($("#switch").prop("checked") == true){
            status = "1";
            //$("#textstatus").text("Excl. BTW");
        }else{
            status = "0";
            //$("#textstatus").text("Incl. BTW");
        }
        $.ajax({
            showLoader: true,
            url: AjaxUrl,
            data: {
                'status':status
            },
            type: "POST"
        }).done(function (data) {
            console.log("Sucess");
            console.log(data);
            location.reload();
            return true;    
        });
    });
});