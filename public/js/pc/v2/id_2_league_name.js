
var MATCH_LEAGUE_IDS = {
        '1-46': {
        	'name_en':'zhongchao', 
        	'sid':1002,
        	'name':'中超'
        },
        '1-31': {
        	'name_en':'yingchao',
        	'sid':1000,
        	'name':'英超'
        },
        '1-26': {
        	'name_en':'xijia',
        	'sid':1003,
        	'name':'西甲'
        },
        '1-29': {
        	'name_en':'yijia',
        	'sid':1004,
        	'name':'意甲'
        },
        '1-11': {
        	'name_en':'fajia',
        	'sid':1005,
        	'name':'法甲'
        },
        '1-8': {
        	'name_en':'dejia',
        	'sid':1006,
        	'name':'德甲'
        },
        '1-73': {
        	'name_en':'uefacl',
        	'sid':1001,
        	'name':'欧冠杯'
        },
        '1-77': {
        	'name_en':'uefael',
        	'sid':1011,
        	'name':'欧罗巴杯'
        },
        '1-139': {
        	'name_en':'afccl',
        	'sid':1007,
        	'name':'亚冠杯'
        },
        '2-1': {
        	'name_en':'nba',
        	'sid':1009,
        	'name':'NBA'
        },
        '2-4': {
        	'name_en':'cba',
        	'sid':1010,
        	'name':'CBA'
        }
};


function FindLeagueName(typeId,leagueID) {
	var findID = '' + typeId + '-' + leagueID + '';

	if(MATCH_LEAGUE_IDS[findID]){
		return MATCH_LEAGUE_IDS[findID]
	}else{
		return false
	}
}
























