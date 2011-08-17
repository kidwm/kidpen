jQuery(function($){
	
	if($("#form").html() == null)
		$("#wrapper").css("min-height", $(document).height()+"px");
	
	if(ie === false){
		var body = $("body");
		
		var s = $('#menu').css({position: "absolute", top: ($("#admin_info").height()+40)+"px", width: "100%"}).offset().top;
		$(window).scroll(function (){
			$("#menu").stop().animate({top : $(window).scrollTop() + s + "px" }, 500);
		});
		
		// title
		var titleTimmer,
		$theA,
		$tooltip = $("<div id=\"tooltip\"><span class=\"triangle\"></span><article></article></div>").css("opacity", 0);
		
		$("#header a, .tooltip").each(function(){
			$(this).attr("titlex", this.title);
			this.title = "";
		}).hover(function(){
			$theA = $(this);
			var offset = $theA.offset(),
			left = offset.left - 80 + $theA.width() * 0.5;
			$tooltip.children("article").text($theA.attr("titlex"));
			var width = $tooltip.width() / 2;
			$tooltip.css({top: (offset.top + $theA.height() + 15) + "px", left: (left + (($tooltip.attr("lastPosition") > left)? 100: -100)) + "px"});
			titleTimmer = setTimeout(function(){
				body.append($tooltip);
				$tooltip.animate({opacity: 1, left: left + "px"}, 400);
			}, 300);
		}, function(){
			clearTimeout(titleTimmer);
			$tooltip.attr("lastPosition", $tooltip.offset().left).css("opacity", 0).remove();
		});
		
		// form
		var input = $("#form form p"),
		submitTimmer,
		submit = input.last().children("input"),
		closeBtn = $("<span class=\"close\">CLOSE</span>").css("display", "none");
		
		input.not(":last").hide(0, function(){
			if($("#wrapper").height() < $(window).height())
				$("#wrapper").css("min-height", $(document).height()+"px");
		}).parents("form").append(closeBtn);
		input.last().addClass("lock").click(function(){
			if($(this).hasClass("lock")){
				$(this).removeClass("lock").children("input").attr("value", "SUBMIT");
				input.not(":last").stop(false, true).slideDown(400);
				closeBtn.show();
				return false;
			}
		}).children("input").attr("value", submit.attr("jshow"));
		
		closeBtn.click(function(){
			input.not(":last").stop(false, true).slideUp(400);
			closeBtn.hide();
			input.last().addClass("lock").children("input").attr("value", submit.attr("jshow"));
		}).mouseenter(function(){
			clearTimeout(submitTimmer);
		});
		
		$("#form.show form").each(function(){
			input.last().children("input").click();
		});
		
		if(input.last().hasClass("lock")){
			if(window.location.hash.substring(1) == "open")
				input.last().children("input").click();
		}
		
		// message
		var msgWrap = $("#message"),
		message = $("#message > article"),
		currentBg = $("<div id=\"current\"></div>"),
		switchSpeed = 800;
		
		firstMsg = message.first().addClass("on");
		
		firstMsg.bind("layoutchange", function(){
			msgWrap.append(currentBg);
			currentBg.css({left: "-1px", top: (firstMsg.offset().top - msgWrap.offset().top - 1) + "px", height: (firstMsg.height() + 2) + "px"}).fadeIn("fast");
		});
		
		firstMsg.css({"z-index":1}).trigger("layoutchange");
		
		message.click(function(){
			var $this = $(this);
			$this.addClass("on").siblings(".on").removeClass("on");
			current = $this.index();
			currentBg.stop().css("z-index", 2).animate({left: ($this.offset().left - msgWrap.offset().left - 1) + "px", top: ($this.offset().top - msgWrap.offset().top - 1) + "px", width: ($this.width() + 2) + "px", height: ($this.height() + 2) + "px"}, {duration: switchSpeed, easing: "easeOutQuart", complete: function(){
				currentBg.css("z-index", 0);
			}});
		});
		
		// keydown
		var current = message.siblings(".on").index(),
		lastIndex = message.length - 1,
		nextMsg = function(){
			if(current < lastIndex){
				$("html, body").stop().animate({scrollTop: (message.eq(current + 1).offset().top - 40) + "px"}, {duration: switchSpeed, easing: "easeOutQuart"});
				message.eq(current + 1).click();
			}
		},
		prevMsg = function(){
			if(current > 0){
				$("html, body").stop().animate({scrollTop: (message.eq(current - 1).offset().top - 40) + "px"}, {duration: switchSpeed, easing: "easeOutQuart"});
				message.eq(current - 1).click();
			}
		},
		pressEnter = function(){
			if(message.eq(current).hasClass("reply"))
				return false;
			var url;
			if(url = message.eq(current).find("header > span > a").attr("href"))
				document.location.href = url+"#open";
		},
		switchMsg = function(event){
			switch(event.keyCode){
				case 13:
					pressEnter();
					break;
				case 74:
					nextMsg();
					break;
				case 75:
					prevMsg();
					break;
			}
		};
		
		$(document).keydown(switchMsg);
		
		$("input, textarea").focus(function(){
			$(document).unbind("keydown", switchMsg);
		}).blur(function(){
			$(document).keydown(switchMsg);
		});
	}
});