var FormVerify = {};

FormVerify = (function () {

	let empty = function (obj) {

		if($(obj)[0].nodeName !== 'INPUT')
		{
			// select
			if(!$(obj).val())
			{
				console.log('select false');
				return false
			}

			return true;
		}
		else
		{
			if($(obj).attr('type') === 'file')
			{
				if ($(obj)[0].files.length == 0)
				{
					console.log('file false');
					return false;
				}

				return true;
			}
			else
			{
				if (!$(obj).val())
				{
					console.log('input false');
					return false;
				}

				return true;
			}
		}

	};

	return {
		empty: empty
	};
}());