$(function() {
	$('#twitter').append($('<div></div>', {
		'id' : 'wrap',
	}));
	$('#wrap').append($('<h2></h2>', {
		'text' : '人気投稿'
	}));
	$('#wrap').append($('<div></div>', {
		'id' : 'popular'
	}));
	$('#wrap').append($('<h2></h2>', {
		'text' : '最新投稿'
	}));
	$('#wrap').append($('<div></div>', {
		'id' : 'recent'
	}));
	$('#twitter').append($('<button></button>', {
		'id' : 'more',
		'text' : 'もっと見る'
	}));
	
	/*
	filter = 0 : フィルターなし
	filter = 1 : whiteList = 1 のみ表示
	filter = 2 : blackList = 0 のみ表示
	*/
	var filter = 2;
	var media_open = function() {
		if (!overlay) {
			var scroll_event = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
			$(document).on(scroll_event, function(e) {
				e.preventDefault();
			});
			$(document).on('touchmove.noScroll', function(e) {
				e.preventDefault();
			});
			$('body').append($('<div></div>', {
				"id" : "screen",
				"css" : {
					"width" : document.body.clientWidth,
					"height" : document.body.clientHeight,
				},
			}));
			$('#screen').append($('<div></div>', {
				"id" : "floatview",
				"css" : {
					"width" : Math.min(600, window.innerWidth * 0.8) + "px",
					"margin" : (window.innerHeight * 0.1 + window.scrollY - 20) + "px auto",
				},
			}));
			$('#floatview').append($("<a></a>", {
				"id" : "userpicwrap",
				"href" : $(this).attr("link"),
				"target" : "_blank",
			}));
			$('#userpicwrap').append($("<img/>", {
				"class" : "userpic",
				"src" : $(this).find('.userpic').attr('src'),
			}));
			$('#floatview').append($("<p></p>", {
				"class" : "user",
				"text" : $(this).find('.user').text(),
			}));
			$('#floatview').append($("<p><a></a></p>", {
				"class" : "suser",
				"text" : $(this).find('.suser').text(),
			}));
			$('#floatview').append($("<p></p>", {
				"class" : "fav",
				"text" : $(this).find('.fav').text(),
			}));
			if (0 < $(this).find(".media").length) {
				$('#floatview').append($("<img/>", {
					"class" : "media",
					"src" : $(this).find('.media').attr('link'),
					"css" : {
						"max-width" : $(this).find('.media').css('width'),
					}
				}));
			}
			$('#floatview').append($("<div></div>", {
				"class" : "text",
				"text" : $(this).children('.text').text(),
			}));

			$('#floatview').append($("<p></p>", {
				"class" : "fdate",
				"text" : $(this).find('.date').text(),
			}));

			$('#floatview').append($("<img/>", {
				"id" : "close",
				"src" : "close.png",
			}));
			var marginT = (window.innerHeight - $("#floatview").innerHeight()) / 2 + window.scrollY;
			$("#floatview").css({
				//"margin" : marginT + "px " + (window.innerWidth * 0.35 - 20) + "px",
			});
			$("#close").on("click", media_close);
			$('#floatview').animate({
				"opacity" : 1,
			}, 300);
		}
	};

	var media_close = function() {
		$("#screen").remove();
		var scroll_event = 'onwheel' in document ? 'wheel' : 'onmousewheel' in document ? 'mousewheel' : 'DOMMouseScroll';
		$(document).off(scroll_event);
		$(document).off('.noScroll');
	};

	function wrap_height_adjust() {
		var tmp = 0;
		$('#wrap').children().each(function() {
			tmp += $(this).outerHeight(true);
		});
		$('#wrap').css({
			'height' : tmp,
		});
	};
	$(window).on('resize', function() {
		wrap_height_adjust();
	});

	var overlay = false;
	$.ajax({
		'type' : 'GET',
		'url' : './db_sortp.php',
		'data' : {
			'sort' : 'fav',
			'order' : 'desc',
			'filter' : filter,
		},
		'dataType' : 'jsonp',
		'jsonpCallback' : 'popular',
		'success' : function(popular) {
			var obj = popular;
			if (0 < obj.length) {
				var num = 10;
				if (window.innerWidth <= 1024) {
					num = 8;
				}
				if (window.innerWidth <= 768) {
					num = 9;
				}
				if (window.innerWidth <= 640) {
					num = 10;
				}
				for (var i = 0; i < Math.min(num, obj.length); i++) {
					$('#popular').append($('<div></div>', {
						"id" : "p_media" + i,
						"class" : "mediawrap",
						"onclick" : "return false;",
						"link" : "https://twitter.com/" + obj[i].user.screen_name + "/status/" + obj[i].id_str,
					}));
					if (obj[i].entities.media != null) {
						$('#p_media' + i).append($('<a></a>', {
							"href" : "javascript:void(0)",
							"target" : "_blank",
							"onclick" : "return false;",
						}));
						$('#p_media' + i + ' a').append($('<img/>', {
							"link" : obj[i].entities.media[0].media_url,
							"class" : "media",
							"css" : {
								"display" : "none",
								"width" : obj[i].entities.media[0].sizes.small.w,
								"max-width" : Math.min(600, window.innerWidth * 0.8) + "px"
							}
						}));
					}
					$('#p_media' + i).append($('<p></p>', {
						"class" : "text",
						"html" : obj[i].text,
					}));
					$('#p_media' + i).append($('<div></div>', {
						"class" : "mediainfo",
					}));

					$('#p_media' + i + ' .mediainfo').append($('<img/>', {
						"src" : obj[i].user.profile_image_url,
						"class" : "userpic",
					}));
					$('#p_media' + i + ' .mediainfo').append($('<p></p>', {
						"class" : "user",
						"html" : obj[i].user.name,
					}));
					//screen_name
					$('#p_media' + i + ' .mediainfo').append($('<p></p>', {
						"class" : "suser",
						"html" : obj[i].user.screen_name,
					}));
					$('#p_media' + i + ' .mediainfo').append($('<p></p>', {
						"class" : "date",
						"html" : obj[i].created_at,
					}));
					$('#p_media' + i + ' .mediainfo').append($('<p></p>', {
						"class" : "fav",
						"html" : "&#9829; : " + obj[i].favorite_count,
					}));
				}
			}

			$.ajax({
				'type' : 'GET',
				'url' : './db_sortp.php',
				'data' : {
					'sort' : 'created_at',
					'order' : 'desc',
					'filter' : filter,
				},
				'dataType' : 'jsonp',
				'jsonpCallback' : 'recent',
				'success' : function(recent) {
					var obj = recent;
					if (0 < obj.length) {
						for (var i = 0; i < Math.min(num, obj.length); i++) {
							$('#recent').append($('<div></div>', {
								"id" : "r_media" + i,
								"class" : "mediawrap",
								"onclick" : "return false;",
								"link" : "https://twitter.com/" + obj[i].user.screen_name + "/status/" + obj[i].id_str,
							}));
							if (obj[i].entities.media != null) {
								$('#r_media' + i).append($('<a></a>', {
									"href" : "javascript:void(0)",
									"target" : "_blank",
									"link" : obj[i].link,
									"onclick" : "return false;"
								}));
								$('#r_media' + i + ' a').append($('<img/>', {
									"link" : obj[i].entities.media[0].media_url,
									"class" : "media",
									"css" : {
										"display" : "none",
										"width" : obj[i].entities.media[0].sizes.small.w,
										"max-width" : Math.min(600, window.innerWidth * 0.8) + "px",
										"resize" : obj[i].entities.media[0].sizes.small.resize,
									}
								}));
							}
							$('#r_media' + i).append($('<p></p>', {
								"class" : "text",
								"html" : obj[i].text,
							}));

							$('#r_media' + i).append($('<div></div>', {
								"class" : "mediainfo",
							}));
							$('#r_media' + i + ' .mediainfo').append($('<img/>', {
								"src" : obj[i].user.profile_image_url,
								"class" : "userpic",
							}));
							$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
								"class" : "user",
								"html" : obj[i].user.name,
							}));
							//screen_name
							$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
								"class" : "suser",
								"html" : obj[i].user.screen_name,
							}));

							//date
							$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
								"class" : "date",
								"html" : obj[i].created_at,
							}));

							$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
								"class" : "fav",
								"html" : "&#9829; : " + obj[i].favorite_count,
							}));
						}
					}
					wrap_height_adjust();
					var timer = null;
					$('#more').on('click', function() {
						var num = 5;
						if (window.innerWidth <= 1024) {
							num = 4;
						}
						if (window.innerWidth <= 768) {
							num = 3;
						}
						if (window.innerWidth <= 640) {
							num = 2;
						}
						if (timer == null) {
							timer = window.setTimeout(function() {
								var pic_cnt = $('#recent .mediawrap').length;
								var append = 0;
								if (pic_cnt % num != 0) {
									append = num - pic_cnt % num;
								} else {
									append = (num < 4 ? num * 2 : num)
								}
								for (var i = pic_cnt; i < Math.min(pic_cnt + append, obj.length); i++) {
									$('#recent').append($('<div></div>', {
										"id" : "r_media" + i,
										"class" : "mediawrap",
										"onclick" : "return false;",
										"link" : "https://twitter.com/" + obj[i].user.screen_name + "/status/" + obj[i].id_str,
										"css" : {
											opacity : 0,
										},
									}));
									$('#r_media' + i).append($("<a></a>", {
										"href" : "javascript:void(0)",
										"target" : "_blank",
										"link" : obj[i].link,

									}));
									if (obj[i].entities.media != null) {
										$('#r_media' + i + ' a').append($("<img/>", {
											"link" : obj[i].entities.media[0].media_url,
											"class" : "media",
											"css" : {
												"display" : "none",
												"width" : obj[i].entities.media[0].sizes.small.w,
												"max-width" : Math.min(600, window.innerWidth * 0.8) + "px",
												"resize" : obj[i].entities.media[0].sizes.small.resize,
											}
										}));
									}
									$('#r_media' + i).append($('<p></p>', {
										"class" : "text",
										"html" : obj[i].text,
									}));
									$('#r_media' + i).append($('<div></div>', {
										"class" : "mediainfo",
									}));
									$('#r_media' + i + ' .mediainfo').append($('<img/>', {
										"src" : obj[i].user.profile_image_url,
										"class" : "userpic",
									}));
									$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
										"class" : "user",
										"html" : obj[i].user.name,
									}));
									//screen_name
									$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
										"class" : "suser",
										"html" : obj[i].user.screen_name,
									}));

									$('#r_media' + i + ' .mediainfo').append($('<p></p>', {
										"class" : "date",
										"html" : obj[i].created_at,
									}));

									$('#r_media' + i + ' .mediainfo').append($("<p></p>", {
										"class" : "fav",
										"html" : "&#9829; : " + obj[i].favorite_count,
									}));

									wrap_height_adjust();
									$(document).on('click', '#r_media' + i, media_open);
									$('.mediawrap').each(function() {
										if ($(this).css("opacity") == 0) {
											$(this).animate({
												opacity : '1'
											}, 300);
										}
									});
								}
								timer = null;
							}, 1000);
						}
					});
					$('.mediawrap').on('click', media_open);
				},
				"error" : function(error) {
					//console.log(error);
				},
			});

			var resize = null;
		},
		"error" : function(error) {
			console.log(error);
		},
	});
});
