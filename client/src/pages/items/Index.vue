<template>
  <div>
    <h1 class="text-center my-5">My items</h1>
    <ul class="list-group list-group-light">
      <draggable
        v-model="items"
        group="items"
        v-on:start="() => (drag = true)"
        v-on:end="() => (drag = false)"
        item-key="id"
        v-on:change="moveItem"
      >
        <template #item="{ element }">
          <li
            class="list-group-item d-flex justify-content-between align-items-center"
          >
            <input
              type="text"
              class="form-control my-2"
              v-model="element.name"
              style="margin-right: 15px"
            />

            <div>{{ element.order }}</div>

            <div class="controls d-flex">
              <button
                class="btn btn-sm btn-primary"
                v-on:click="updateItem(element.id, element.name)"
              >
                &#9998;
              </button>

              <button
                class="btn btn-sm btn-danger mx-1"
                v-on:click="deleteItem(element.id)"
              >
                X
              </button>

              <button
                class="btn btn-sm btn-secondary"
              >
                &#10055;
              </button>
            </div>
          </li>
        </template>
      </draggable>

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
import { ref, computed } from "vue";
import draggable from "vuedraggable";

import axios from "axios";

axios.defaults.withCredentials = true;

export default {
  components: {
    draggable,
  },
  setup() {
    const items = ref([]);
    const orderList = ref([]);
    const newItemName = ref();
    const drag = ref(false);
    const endpoint = "http://localhost/api/items";

    axios
      .get(endpoint)
      .then((response) => {
        items.value = response.data.items;
      })
      .catch(() => {
        // window.location = "/sign-in";
      });

    function displayAlert(error, message) {
      if (error.response?.data?.message) {
        alert(error.response.data.message);
      } else {
        alert(message);
      }
    }

    const addItem = () => {
      axios
        .post(endpoint, {
          name: newItemName.value,
        })
        .then((response) => {
          newItemName.value = "";

          items.value.push(response.data.item);
        })
        .catch((error) => displayAlert(error, "Failed to add item"));
    };

    const deleteItem = (id) => {
      axios
        .delete(`${endpoint}/${id}`)
        .then(() => {
          items.value = items.value.filter((item) => item.id !== id);
        })
        .catch((error) => displayAlert(error, "Failed to delete item"));
    };

    const updateItem = (id, name) => {
      axios
        .put(`${endpoint}/${id}`, {
          name,
        })
        .then(() => {
          const item = items.value.find((item) => item.id === id);
          item.name = name;
        })
        .catch((error) => displayAlert(error, "Failed to update item"));
    };

    const moveItem = ({ moved }) => {
      axios
        .put(`${endpoint}/${moved.element.id}/move-to/${moved.newIndex}`)
        .then(() => {})
        .catch((error) => displayAlert(error, "Failed to move item"));
    };

    return {
      orderList,
      drag,
      items,
      newItemName,
      addItem,
      deleteItem,
      updateItem,
      moveItem,
    };
  },
};
</script>
