'use strict';

// var averageClimate = {
// 	britishVirginIslands : { January : { AvgTemp : '28', Rainfall, '68'}, February : {AvgTemp : '21', Rainfall, '38'}, March : {AvgTemp : '21', Rainfall, '38'}
// 	}
// }

var climateMonths = ["January","February","March","April","May","June","July","August","September","October","November","December"];


var averageClimate = {
	britishVirginIslands : {
		January : { AvgTemp : '25', Rainfall : '74.2'},
		February : { AvgTemp : '25', Rainfall : '63.2'},
		March : { AvgTemp : '25', Rainfall : '55.4'},
		April : { AvgTemp : '26', Rainfall : '84.6'},
		May : { AvgTemp : '27', Rainfall : '116.6'},
		June : { AvgTemp : '27.5', Rainfall : '70.6'},
		July : { AvgTemp : '28', Rainfall : '83.1'},
		August : { AvgTemp : '28', Rainfall : '112'},
		September : { AvgTemp : '28', Rainfall : '156'},
		October : { AvgTemp : '27.5', Rainfall : '133.4'},
		November : { AvgTemp : '27', Rainfall : '176.8'},
		December : { AvgTemp : '25.5', Rainfall : '112'}
	},
	leewardIslands : {
		January : { AvgTemp : '26', Rainfall : '470'},
		February : { AvgTemp : '26', Rainfall : '260'},
		March : { AvgTemp : '26', Rainfall : '120'},
		April : { AvgTemp : '26', Rainfall : '210'},
		May : { AvgTemp : '26', Rainfall : '30'},
		June : { AvgTemp : '25', Rainfall : '40'},
		July : { AvgTemp : '24', Rainfall : '200'},
		August : { AvgTemp : '25', Rainfall : '50'},
		September : { AvgTemp : '25', Rainfall : '190'},
		October : { AvgTemp : '26', Rainfall : '60'},
		November : { AvgTemp : '26', Rainfall : '180'},
		December : { AvgTemp : '26', Rainfall : '390'}
	},
	windwardIslands : {
		January : { AvgTemp : '27', Rainfall : '300'},
		February : { AvgTemp : '27', Rainfall : '230'},
		March : { AvgTemp : '27', Rainfall : '170'},
		April : { AvgTemp : '27', Rainfall : '130'},
		May : { AvgTemp : '26', Rainfall : '100'},
		June : { AvgTemp : '25', Rainfall : '60'},
		July : { AvgTemp : '25', Rainfall : '50'},
		August : { AvgTemp : '24', Rainfall : '40'},
		September : { AvgTemp : '25', Rainfall : '60'},
		October : { AvgTemp : '25', Rainfall : '80'},
		November : { AvgTemp : '25', Rainfall : '150'},
		December : { AvgTemp : '26', Rainfall : '260'}
	},
	westMediterranean : {
		January : { AvgTemp : '12.5', Rainfall : '71.6'},
		February : { AvgTemp : '12.6', Rainfall : '65.4'},
		March : { AvgTemp : '13.5', Rainfall : '59.5'},
		April : { AvgTemp : '15.7', Rainfall : '44.1'},
		May : { AvgTemp : '18.9', Rainfall : '25.5'},
		June : { AvgTemp : '22.4', Rainfall : '12.2'},
		July : { AvgTemp : '25.6', Rainfall : '5.1'},
		August : { AvgTemp : '26.2', Rainfall : '13.3'},
		September : { AvgTemp : '24.1', Rainfall : '41.5'},
		October : { AvgTemp : '20.4', Rainfall : '98'},
		November : { AvgTemp : '16.8', Rainfall : '94.3'},
		December : { AvgTemp : '13.8', Rainfall : '80'}
	},
	eastMediterranean : {
		January : { AvgTemp : '7', Rainfall : '139'},
		February : { AvgTemp : '8', Rainfall : '125'},
		March : { AvgTemp : '10', Rainfall : '104'},
		April : { AvgTemp : '13', Rainfall : '104'},
		May : { AvgTemp : '17', Rainfall : '75'},
		June : { AvgTemp : '21', Rainfall : '48'},
		July : { AvgTemp : '24', Rainfall : '26'},
		August : { AvgTemp : '24', Rainfall : '38'},
		September : { AvgTemp : '21', Rainfall : '101'},
		October : { AvgTemp : '17', Rainfall : '162'},
		November : { AvgTemp : '12', Rainfall : '198'},
		December : { AvgTemp : '9', Rainfall : '178'}
	},
	maine : {
		January : { AvgTemp : '-5.5', Rainfall : '95'},
		February : { AvgTemp : '-4.4', Rainfall : '92'},
		March : { AvgTemp : '0.4', Rainfall : '102'},
		April : { AvgTemp : '6.2', Rainfall : '102'},
		May : { AvgTemp : '11.8', Rainfall : '90'},
		June : { AvgTemp : '17.1', Rainfall : '86'},
		July : { AvgTemp : '20.3', Rainfall : '78'},
		August : { AvgTemp : '19.6', Rainfall : '73'},
		September : { AvgTemp : '15.3', Rainfall : '85'},
		October : { AvgTemp : '9.5', Rainfall : '98'},
		November : { AvgTemp : '3.9', Rainfall : '115'},
		December : { AvgTemp : '-2.7', Rainfall : '105'}		
	},
	newport : {
		January : { AvgTemp : '-0.7', Rainfall : '94'},
		February : { AvgTemp : '0.2', Rainfall : '88.9'},
		March : { AvgTemp : '3.5', Rainfall : '137.2'},
		April : { AvgTemp : '8.3', Rainfall : '116.8'},
		May : { AvgTemp : '13.4', Rainfall : '91.4'},
		June : { AvgTemp : '18.5', Rainfall : '88.9'},
		July : { AvgTemp : '21.9', Rainfall : '86.4'},
		August : { AvgTemp : '21.7', Rainfall : '96.5'},
		September : { AvgTemp : '18.5', Rainfall : '101.6'},
		October : { AvgTemp : '12.8', Rainfall : '104.1'},
		November : { AvgTemp : '7.8', Rainfall : '114.3'},
		December : { AvgTemp : '2.4', Rainfall : '111.8'}		
	},
};

// var britishVirginIslands : { January : { AvgTemp : '28', Rainfall : '68'}, February : { AvgTemp : '21', Rainfall : '38'} }