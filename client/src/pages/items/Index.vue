<template>
  <div>
    <h1 class="text-center my-5">My items</h1>
    <ul class="list-group list-group-light">
      <li
        class="list-group-item d-flex justify-content-between align-items-center"
        v-for="(item, key) in items"
        :key="item.id"
      >
        <input
          type="text"
          class="form-control my-2"
          v-model="item.name"
          style="margin-right: 15px"
        />

        <div class="controls d-flex">
          <button
            class="btn btn-sm btn-primary"
            v-on:click="updateItem(item.id, item.name)"
          >
            &#9998;
          </button>

          <button
            class="btn btn-sm btn-danger mx-1"
            v-on:click="deleteItem(item.id)"
          >
            X
          </button>

          <button
            class="btn btn-sm btn-secondary mx-1"
            v-on:click="move(item.id, 'up')"
            :disabled="key === 0"
          >
            &uarr;
          </button>

          <button
            class="btn btn-sm btn-secondary"
            v-on:click="move(item.id, 'down')"
            :disabled="key >= items.length - 1"
          >
            &darr;
          </button>
        </div>
      </li>

      <li
        class="list-group-item d-flex justify-content-between align-items-center"
        v-if="!items.length"
      >
        No items found. Please add one using the input below.
      </li>
    </ul>

    <div class="row mt-2">
      <div class="col">
        <div class="form-group">
          <input
            type="text"
            class="form-control"
            v-model="newItemName"
            placeholder="New item name"
          />
        </div>
      </div>

      <div class="col-auto">
        <button
          type="button"
          class="btn btn-primary btn-block"
          v-on:click="addItem"
        >
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from "vue";
import axios from "axios";

axios.defaults.withCredentials = true;

export default {
  setup() {
    const items = ref([]);
    const newItemName = ref();

    axios
      .get("http://localhost/api/items")
      .then((response) => {
        items.value = response.data.items;
      })
      .catch(() => {
        window.location = "/sign-in";
      });

    const addItem = () => {
      axios
        .post("http://localhost/api/items", {
          name: newItemName.value,
        })
        .then((response) => {
          newItemName.value = "";

          items.value.push(response.data.item);
        })
        .catch((error) => {
          if (error.response?.data?.message) {
            alert(error.response.data.message);
          } else {
            alert("Error adding item");
          }
        });
    };

    const deleteItem = (id) => {
      axios
        .delete(`http://localhost/api/items/${id}`)
        .then(() => {
          items.value = items.value.filter((item) => item.id !== id);
        })
        .catch((error) => {
          if (error.response?.data?.message) {
            alert(error.response.data.message);
          } else {
            alert("Error deleting item");
          }
        });
    };

    const move = (id, direction) => {
      axios
        .put(`http://localhost/api/items/${id}/move-${direction}`)
        .then(() => {
          const item = items.value.find((item) => item.id === id);
          const index = items.value.indexOf(item);

          if (direction === "up") {
            items.value.splice(index - 1, 0, items.value.splice(index, 1)[0]);
          } else {
            items.value.splice(index + 1, 0, items.value.splice(index, 1)[0]);
          }
        })
        .catch((error) => {
          if (error.response?.data?.message) {
            alert(error.response.data.message);
          } else {
            alert("Error moving item");
          }
        });
    };

    const updateItem = (id, name) => {
      axios
        .put(`http://localhost/api/items/${id}`, {
          name,
        })
        .then(() => {
          const item = items.value.find((item) => item.id === id);
          item.name = name;
        })
        .catch((error) => {
          if (error.response?.data?.message) {
            alert(error.response.data.message);
          } else {
            alert("Error updating item");
          }
        });
    };

    return {
      items,
      newItemName,
      addItem,
      deleteItem,
      move,
      updateItem,
    };
  },
};
</script>
