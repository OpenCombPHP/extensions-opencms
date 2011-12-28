jQuery(function(){
	jQuery('.topList').each(function(b,v){
		resetTitleLen(v);
	});
	jQuery('.topList').resize(function(){
		resetTitleLen(this);
	});
	
	function resetTitleLen(aTopList){
		var aTopList = jQuery(aTopList);
		
		//如果宽度没有变化就退出
		if( aTopList.data('oldWidth') && aTopList.width() == aTopList.data('oldWidth')){
			return;
		}
		aTopList.data('oldWidth',aTopList.width());
		
		//字符容器的宽度
		var arrLis = aTopList.find('ul>li');
		var nLiLen = arrLis.first().width();
		
		//时间栏目的宽度
		var arrDataTimes = arrLis.find('>span');
		var nDataTimeLen = arrDataTimes.first().width();
		
		//获取所有的链接
		var arrAs = arrLis.find(">a");
		arrAs.each(function(b,v){
			jQuery(v).width(nLiLen - nDataTimeLen - 8);
		});
	}
});