function pwStrength(pwd) {
    O_color = "#eeeeee";
    L_color = "#ff1b1b";
    M_color = "#ff991b";
    H_color = "#1bff22";
    if (pwd == null || pwd == '') {
        Lcolor = Mcolor = Hcolor = O_color;
    } else {
        S_level = checkStrong(pwd);
        switch (S_level) {
        case 0:
            Lcolor = Mcolor = Hcolor = O_color;
        case 1:
            Lcolor = L_color;
            Mcolor = Hcolor = O_color;
            break;
        case 2:
            Lcolor = M_color;
            Mcolor = M_color;
            Hcolor = O_color;
            break;
        default:
            Lcolor = H_color;
            Mcolor = H_color;
            Hcolor = H_color;
        }
    }
    $("#strength_L").css('background-color', Lcolor);
    $("#strength_M").css('background-color', Mcolor);
    $("#strength_H").css('background-color', Hcolor);
    return;
}

//判断输入密码的类型
function CharMode(iN) {
    if (iN >= 48 && iN <= 57) //数字
    return 1;
    if (iN >= 65 && iN <= 90) //大写
    return 2;
    if (iN >= 97 && iN <= 122) //小写
    return 4;
    else return 8;
}
//bitTotal函数
//计算密码模式
function bitTotal(num) {
    modes = 0;
    for (i = 0; i < 4; i++) {
        if (num & 1) modes++;
        num >>>= 1;
    }
    return modes;
}
//返回强度级别
function checkStrong(sPW) {
    if (sPW.length <= 4) return 0; //密码太短
    Modes = 0;
    for (i = 0; i < sPW.length; i++) {
        //密码模式
        Modes |= CharMode(sPW.charCodeAt(i));
    }
    return bitTotal(Modes);
}
function pre_checkpassword(id){
    var inputBox=document.getElementById(id).value;
    pwStrength(inputBox);
}
function ChangeVisible(id){
    var Elem=document.getElementById(id);
    var Eye=document.getElementById(id+'Eye');
    if(Elem.type=='password'){
        Elem.type='text';
        Eye.innerHTML='隐藏';
    }else{
        Elem.type='password';
        Eye.innerHTML='查看';
    }
}