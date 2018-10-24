$(document).ready(function() {
	$("#btnSubmit").click(function(event) {
		event.preventDefault();
		var name = $('#inputName').val();
		var id = $('#inputID').val();
		var tel = $('#inputTel').val();
		var grade = $('#selectGrade').val();
		var college = $('#selectCollege').val();
		var intro = $('#inputIntro').val();
		if (grade != 'null' && college != 'null' && grade.length == 4
			&& 0 < name.length && name.length <= 20
			&& 0 < id.length && id.length <= 12
			&& 0 < tel.length && tel.length <= 11
			&& 0 < college.length && college.length <= 11
			&& 0 < intro.length && intro.length <= 200) {
			$.post('/join/api/reg', {
				'name': name,
				'id': id,
				'tel': tel,
				'grade': grade,
				'college': college,
				'intro': intro
			}, function(data) {
				var res = JSON.parse(data);
				switch (res['code']) {
				case 0:
					$('#popMsg1').text(res['msg']);
					$('#popMsg2').text('开源社区公开群：247736999');
					$('#popBtn').replaceWith('<a href="https://oss.lzu.edu.cn/" id="popBtn" class="btn btn-primary" role="button">参观</a>');
					$('#popModal').modal('show');
					break;
				case 1:
					$('#popMsg1').text('申请失败…');
					$('#popMsg2').text(res['msg']);
					$('#popBtn').replaceWith('<button id="popBtn" type="button" class="btn btn-default" data-dismiss="modal">好哒</button>');
					$('#popModal').modal('show');
					break;
				}
			});
		} else {
			$('#popMsg1').text('申请失败…');
			$('#popMsg2').text('请再检查一下');
			$('#popBtn').replaceWith('<button id="popBtn" type="button" class="btn btn-default" data-dismiss="modal">好哒</button>');
			$('#popModal').modal('show');
		}
	});
	$("a > #popBtn").click(function() {
		window.location.href = "https://oss.lzu.edu.cn/";
	});
});