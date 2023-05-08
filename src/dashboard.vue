<template>
    <div class="container-wrapper flex flex-column w-full overflow-auto pb-4">

        <div class="w-full bg-blue-500 flex align-items-center justify-content-center p-3">
            <h2 class="text-0 m-0 p-0">Liminator Control Panel</h2>
        </div>

        <div class="surface-ground grid grid-nogutter px-4 pt-5">
            <div class="col-12 md:col-6 lg:col-6 pr-3 pb-3">
                <div class="grid grid-nogutter">
                    <label
                        v-html="'Energy Output (W/CM<sup>2</sup>)'"
                        class="overflow-hidden text-overflow-ellipsis font-bold white-space-nowrap col-12 md:col-4 lg:col-4 flex align-items-center bg-blue-500 text-0 border-round-left p-3"
                        for="energy-output">
                    </label>
                    <InputNumber
                        class="col-12 md:col-8 lg:col-8"
                        id="energy-output"
                        v-model="value.energy"
                        inputClass="p-3 border-noround border-round-right"
                        placeholder="Energy Output"
                        @input="onChange"
                        :useGrouping="false"
                        :showButtons="false">
                    </InputNumber>
                </div>
            </div>

            <div class="col-12 md:col-6 lg:col-6 pr-2 pb-3">
                <div class="grid grid-nogutter">
                    <label
                        v-html="'Temperature (&#8451;)'"
                        class="overflow-hidden text-overflow-ellipsis font-bold white-space-nowrap col-12 md:col-4 lg:col-4 flex align-items-center bg-blue-500 text-0 border-round-left p-3"
                        for="temperature">
                    </label>
                    <InputNumber
                        class="col-12 md:col-8 lg:col-8"
                        id="temperature"
                        v-model="value.temperature"
                        inputClass="p-3 border-noround border-round-right"
                        placeholder="Temperature"
                        @input="onChange"
                        :useGrouping="false"
                        :showButtons="false">
                    </InputNumber>
                </div>
            </div>
        </div>

        <div class="grid grid-nogutter">
            <div class="col-12 md:col-4 lg:col-4 surface-ground pl-4 pr-5 pt-3 flex flex-column gap-4">
                <div class="w-full flex flex-column gap-3 pr-2">
                    <label
                        v-html="'Power Control (%)'"
                        class="flex-1 font-bold text-700 overflow-hidden text-overflow-ellipsis "
                        for="power-control">
                    </label>
                    <InputNumber
                        class="flex-1"
                        id="power-control"
                        v-model="value.power"
                        placeholder="Power Control"
                        @input="onChange"
                        inputClass="p-3"
                        :min="0"
                        :max="100"
                        :useGrouping="false"
                        :showButtons="true">
                    </InputNumber>
                </div>

                <div class="w-full flex flex-column gap-3 pr-2">
                    <label
                        v-html="'Speed (m/s)'"
                        class="flex-1 font-bold text-700 overflow-hidden text-overflow-ellipsis "
                        for="speed">
                    </label>
                    <InputNumber
                        class="flex-1"
                        id="speed"
                        inputClass="p-3"
                        v-model="value.speed"
                        placeholder="Speed Control"
                        @input="onChange"
                        :useGrouping="false"
                        :showButtons="true">
                    </InputNumber>
                </div>

                <Button @click="(visibleModal = !visibleModal)" label="Add Data Source" class="p-button-success"></Button>

                <!-- <template v-for="(index) in 6" :key="index">
                    <div class="w-full flex flex-column gap-3 pr-2">
                        <label
                            v-html="`Sensor ${ index }`"
                            class="flex-1 font-bold text-700 overflow-hidden text-overflow-ellipsis "
                            :for="`sensor_${ index }`">
                        </label>
                        <InputNumber
                            class="flex-1"
                            v-model="value.sensor[index]"
                            placeholder="Sensor"
                            @input="onChange"
                            inputClass="p-3"
                            :id="`sensor_${ index }`"
                            :useGrouping="false"
                            :showButtons="false">
                        </InputNumber>
                    </div>
                </template> -->
            </div>

            <div class="col-12 md:col-8 lg:col-8 flex flex-column">
                <div class="pl-4 pr-5 graph-size border-300 surface-ground">
                    <div class="h-full w-full surface-0 border-1 border-300 pt-4 pl-4 pb-3" ref="chartcontainer"></div>
                    <ProgressBar class="surface-ground" :mode="progressMode" style="height: .2em"></ProgressBar>
                </div>
            </div>
        </div>

    </div>

    <Dialog :visible="visibleModal" :modal="true" :closable="false">
        <template #header>
            <div class="w-full flex align-items-center p-3 border-bottom-1 border-300">
                <span class="text-xl font-bold">Add Data Sources</span>
            </div>
        </template>

        <div class="flex flex-column p-3 gap-3 surface-ground pb-5">
            <label class="text-base font-bold text-700 w-full">Label Name</label>
            <InputText v-model="sourceInput.label" class="w-30rem h-3rem border-round-lg px-3" placeholder="Label name (exp: Water Heater)"></InputText>

            <label class="text-base font-bold text-700 w-full">Data Attribute</label>
            <InputText v-model="sourceInput.attribute" class="w-30rem h-3rem border-round-lg px-3" placeholder="Attribute name (exp: temperature_1)"></InputText>
        </div>
        
        <template #footer>
            <div class="w-full flex align-items-center justify-content-end p-3 border-top-1 border-300">
                <Button @click="(visibleModal = false)" label="Close" class="p-button-secondary"></Button>
                <Button @click="submitSource" label="Add Source" class="p-button-success"></Button>
            </div>
        </template>
    </Dialog>
</template>

<script>
import * as echarts from 'echarts';
import rawdata from './dataset.json';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import axios from 'axios';
import moment from 'moment';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import ProgressBar from 'primevue/progressbar';

export default {
    name: 'dashboard',
    components: {
        InputNumber,
        ProgressBar,
        Button,
        Dialog,
        InputText,
    },
    data: () => ({
        sourceData: [],
        sourceInput: {
            label: null,
            attribute: null
        },
        value: {
            sensor: [],
            speed: null,
            power: null,
            energy: null,
            temperature: null,
        },
        chartAction: {
            right: 10,
            feature: {
                dataZoom: {
                    yAxisIndex: 'none'
                },
                restore: {},
                saveAsImage: {}
            }
        },
        chartTitle: {
            text: 'Sail Temperature Graph',
            left: '20'
        },
        trialFinish: '2023-05-15',
        visibleModal: false,
        progressMode: null
    }),
    methods: {
        onChange: function(){
            console.log(this.value);
        },
        fetchAPI: async function(){
            try{
                const request = await axios.post(this.outsource.url);

                return (request.data ?? []);
            }catch(error){
                console.error(error);
            }
        },
        pooling: async function({ callback, validate, interval, maxAttempts }){
            let attempts = 0;

            const executePoll = async (resolve, reject) => {
                const result = await callback();

                attempts++;

                if (validate(result)) {
                    return resolve(result);
                } else if (maxAttempts && attempts === maxAttempts) {
                    return reject(new Error('Exceeded max attempts'));
                } else {
                    setTimeout(executePoll, interval, resolve, reject);
                }
            };

            return new Promise(executePoll);
        },
        submitSource: function(){
            this.sourceData.push({ ...this.sourceInput });

            this.visibleModal = false;
            this.sourceInput.label = null;
            this.sourceInput.attribute = null;
        }
    },
    computed: {
        outsource: function(){
            let baseconfig = {
                url: `${ window.location.pathname }controller.php`,
                refreshRate: 1000,
            };

            if(typeof chartSetup === "function"){ 
                const sources = chartSetup();

                if(sources.url){
                    baseconfig.url = sources.url;
                }
            }

            return baseconfig;
        }
    },
    mounted: function(){
        const chartEl = this.$refs.chartcontainer;
        const myChart = echarts.init(chartEl);
        const isExpired = (moment().isSame(this.trialFinish, 'day'));

        const markliner = {
            silent: true,
            lineStyle: { color: '#333' },
            data: [{ yAxis: 50 }, { yAxis: 100 }, { yAxis: 150 }, { yAxis: 200 }, { yAxis: 300 }]
        };

        const configuration = {
            grid     : { left: '5%', right: '15%', bottom: '10%' },
            title    : this.chartTitle,
            toolbox  : false, //this.chartAction,
            tooltip  : { trigger: 'axis' },
            dataZoom: [],
            visualMap: {
                top: 50,
                right: 10,
                pieces: [
                    { gt: 0, lte: 50, color: '#93CE07' },
                    { gt: 50, lte: 100, color: '#FBDB0F' },
                    { gt: 100, lte: 150, color: '#FC7D02' },
                    { gt: 150, lte: 200, color: '#FD0100' },
                    { gt: 200, lte: 300, color: '#AA069F' },
                    { gt: 300, color: '#AC3B2A' }
                ],
                outOfRange: { color: '#999' },
            },
            yAxis: {},
            xAxis: {},
            series: []
        };

        const callback = () => new Promise(resolve => {
            let caller = this.fetchAPI();
            
            caller.then(response => {
                let chartcollect = [];
                let zoomdata = [];
                let timecollect = response.map(c => c.date);

                this.sourceData.forEach(({ attribute, label }) => {
                    chartcollect.push({
                        name: label,
                        type: 'line',
                        markLine: markliner,
                        data: response.map(function (item) {
                            return (item[attribute] ?? 0);
                        })
                    });
                });

                zoomdata.push({ startValue: timecollect[0] });
                zoomdata.push({ type: 'inside' });

                configuration.dataZoom = zoomdata;
                configuration.series = chartcollect;
                configuration.xAxis.data = timecollect;

                myChart.setOption(configuration);

                this.progressMode = 'indeterminate';
                console.log('calling!');
                resolve();
            });

            caller.catch(err => {
                this.progressMode = null;
                console.error(err);
            });
        });

        const validate = () => {
            return false;
        };

        if(isExpired == false){
            this.pooling({ callback, validate, interval: this.outsource.refreshRate });
        }else{
            document.body.innerHTML = '';
        }
    }
}
</script>

<style lang="scss">
    @import '_core.scss';
    @import '_code.scss';

    .container-wrapper{
        height: 100vh;
    }

    .graph-size{
        height: 80vh;
    }

    .p-dialog-header,
    .p-dialog-footer,
    .p-dialog-content{
        padding: 0 !important;
    }
</style>
  