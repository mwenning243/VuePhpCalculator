<template>
  <div id="app">
    <div class="calculator">
      <div class="display">
        <div class="prev_display_w_operator" id="prev_display">{{previous + " " + operator}}</div>
        <div class="current_display" id="current_display">{{current || '0'}}</div>
      </div>
      <div @click="clearEverything" class="icon modifier">CE</div>
      <div @click="clearLine" class="icon modifier">C</div>
      <div @click="backSpace" class="icon modifier">BS</div>
      <div @click="equation('+')" class="icon operator">+</div>
      <div @click="append('7')" class="icon">7</div>
      <div @click="append('8')" class="icon">8</div>
      <div @click="append('9')" class="icon">9</div>
      <div @click="equation('-')" class="icon operator">-</div>
      <div @click="append('4')" class="icon">4</div>
      <div @click="append('5')" class="icon">5</div>
      <div @click="append('6')" class="icon">6</div>
      <div @click="equation('*')" class="icon operator">*</div>
      <div @click="append('1')" class="icon">1</div>
      <div @click="append('2')" class="icon">2</div>
      <div @click="append('3')" class="icon">3</div>
      <div @click="equation('/')" class="icon operator">/</div>
      <div @click="negate" class="icon">+/-</div>
      <div @click="append('0')" class="icon">0</div>
      <div @click="decimal" class="icon">.</div>
      <div @click="equals" class="icon operator">=</div>
    </div>
    <br>
    <div class="historyContainer">
      <div class="historyEntries" v-for="historyEntry in historyEntries" :key="historyEntry.id">
        <div class="historyEntry entryLoad" @click="loadSelectedEntry(historyEntry)"> Load </div>
        <div class="historyEntry entryValue leftEntryValue">{{historyEntry.previous}}</div>
        <div class="historyEntry entryValue">{{historyEntry.operator}}</div>
        <div class="historyEntry entryValue rightEntryValue">{{historyEntry.current}}</div>
        <div class="historyEntry entryDelete" @click="deleteEntry(historyEntry.id)"> Delete </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { ref } from "vue";
function scalePrevious(length){
  var fontSize = length <= 10 ? 30 : length >= 30 ? 10 : 40 - length;
  document.getElementById("prev_display").style.fontSize = `${fontSize}px`;
}
function scaleCurrent(length){
  var fontSize = length <= 10 ? 40 : length >= 35 ? 10 : 46 - length;
  document.getElementById("current_display").style.fontSize = `${fontSize}px`;
}
export default {
  name: "App",
  setup(){
    let historyEntries = ref([]);
    async function GetData(){
      const { data } = await axios.get("http://localhost:3000/calculator/api/index.php");
      historyEntries.value = data;
    }
    function deleteEntry(id) {
      historyEntries.value.forEach((historyEntry, i) => {
        if (historyEntry.id === id) {
          historyEntries.value.splice(i, 1);
        }
      });
      const params = new URLSearchParams();
      params.append("id", id);
      axios.post("http://localhost:3000/calculator/api/delete.php", params);
    }
    function insertEntry() {
      if (this.previous != "" && this.operator !="" && this.current !=null){
        if (!historyEntries.value) historyEntries.value = [];
        historyEntries.value.push({
          previous: this.previous,
          operator: this.operator,
          current: this.current
        });
        const params = new URLSearchParams();
        params.append("previous", this.previous);
        params.append("operator", this.operator);
        params.append("current", this.current);
        axios.post("http://localhost:3000/calculator/api/insert.php", params);
      }
    }
    GetData();
    return { historyEntries, deleteEntry, insertEntry };
  },
  data(){
    return{
      current: "",
      previous: "",
      operator: "",
      equations: {
        '+': function (x, y) { return x + y },
        '-': function (x, y) { return x - y },
        '*': function (x, y) { return x * y },
        '/': function (x, y) { return x / y },
      }
    }
  },
  methods: {
    loadSelectedEntry(entry){
      this.previous = entry.previous;
      this.operator = entry.operator;
      this.current = entry.current;
      scaleCurrent(this.current.toString().length);
      scalePrevious(this.previous.toString().length);
    },
    clearLine(){
      this.current = '';
      scaleCurrent(this.current.toString().length);
    },
    clearEverything(){
      this.current = '';
      this.previous = '';
      this.operator = '';
      scaleCurrent(this.current.toString().length);
    },
    negate(){
      if (this.current != ''){
        this.current = `${parseFloat(this.current) * -1}`;
        scaleCurrent(this.current.toString().length);
      }
    },
    append(val){
      if (this.current.toString().length >= 50) return;
      if (val != "0" || this.current != ''){
        this.current = `${this.current}${val}`;
        scaleCurrent(this.current.toString().length);
      }
    },
    decimal(){
      if (this.current.toString().indexOf('.') === -1) {
        this.current = `${this.current}.`;
        scaleCurrent(this.current.toString().length);
      }
    },
    backSpace(){
      this.current = this.current.slice(0, -1); 
      scaleCurrent(this.current.toString().length);
    },
    equation(operation){
      if (this.previous != '' && this.operator != ''){
        this.equals();
      }
      if (this.current == ''){
        this.current = '0';
      }
      this.previous = parseFloat(this.current);
      this.operator = operation;
      this.current = '';
      scalePrevious(this.previous.toString().length);
      scaleCurrent(this.current.toString().length);
    },
    equals(){
      if (this.previous.toString() != '' && this.operator != ''){
        if (this.current == ''){
          this.current = 0;
        }
        this.insertEntry();
        this.current = this.equations[this.operator](parseFloat(this.previous), parseFloat(this.current));
        this.previous = '';
        this.operator = '';
        scaleCurrent(this.current.toString().length);
      }
    },
  }
}
</script>

<style scoped>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #180d26;
  margin-top: 0px;
  margin-bottom: 20px;
}
.calculator{
  margin: 0 auto;
  width: 300px;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-auto-rows: minmax(40px, auto);
  font-size: 40px;
  background-color: #180d26;
  padding: 3px;
  border-radius: 10px;
}
.icon{
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #a3a7bf;
  aspect-ratio: 1;
  margin: 1px;
  border-radius: 6px;
}
.display{
  display: grid;
  grid-column: 1 / 5;
  aspect-ratio: 4;
  color: #a3a7bf;
  padding-right: 5px;
}
.prev_display_w_operator{
  font-size: 30px;
  display: flex;
  justify-content: left;
  padding-left: 5px;
}
.current_display{
  display: flex;
  justify-content: right;
}
.operator{
  background-color: #a66342;
}
.modifier{
  background-color: #6b6b99;
}
.historyContainer{
  background-color: #180d26;
  width: 300px;
  margin: 0 auto;
  width: 300px;
  display: grid;
  padding: 2px 3px;
  border-radius: 9px;
}
.historyEntry{
  font-weight: bold;
  overflow: hidden;
  font-size: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  aspect-ratio: 1.5;
  border-radius: 6px;
}
.historyEntries{
  grid-template-columns: repeat(5, 1fr);
  grid-auto-rows: minmax(40px, auto);
  margin: 0 auto;
  width: 300px;
  display: grid;
  margin-top: 1px;
  margin-bottom: 1px;
}
.entryValue{
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #6b6b99;
  border-radius: 0;
}
.entryLoad{
  background-color: #7d9945;
  margin-right: 2px;
  aspect-ratio: 1.45;
}
.leftEntryValue{
  border-top-left-radius: 6px;
  border-bottom-left-radius: 6px;
}
.rightEntryValue{
  border-top-right-radius: 6px;
  border-bottom-right-radius: 6px;
}
.entryDelete{
  background-color: #a64b5a;
  margin-left: 2px;
  aspect-ratio: 1.45;
}

</style>
