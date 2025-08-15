<script setup lang="ts">
import { onMounted, ref, watch, computed } from 'vue'
import { useFieldArray, useForm } from 'vee-validate'
import * as yup from 'yup'
import { makeApiCall } from '@/services/apiService'
import type { Branch, GenericSetUp, Store, Product } from '@/types/globalTypes'
import { formatNumber } from '@/types/globalTypes'

const step = ref(1)
const isLoading = ref(false)
const status = ref<string>('')

// const user = computed(() => useAuth.getUserDetails.user);
const savedUser = localStorage.getItem('user')
const parsedUser = savedUser ? JSON.parse(atob(savedUser)).user : null
const branch = ref<Branch>({ id: parsedUser.branch_id, name: parsedUser.branch }) // Static branch
const store = ref<Store>({ id: parsedUser.store_id, name: parsedUser.store, branch_id: 2, store_type_id: 2 })

// Validation Schema
const schema = yup.object({
  transfer_date: yup.string().required('Transfer Date is required'),
  source_store_id: yup.number().required('Source Store is required').positive('Select a valid store'),
  source_branch_id: yup.number().required('Source Branch is required').positive('Select a valid branch'),
  destination_branch_id: yup.number().required('Destination Branch is required').positive('Select a valid branch'),
  destination_store_id: yup.number()
    .required('Destination Store is required')
    .positive('Select a valid store')
    .test('different-stores', 'Source and destination stores cannot be the same', function (value) {
      const { source_store_id } = this.parent
      if (!value || !source_store_id) {
        return true
      }
      return value !== source_store_id
    }),
  items: yup.array().of(
    yup.object().shape({
      product_id: yup.number().required('Product is required').positive('Select a valid product').label('Item'),
      quantity: yup.number()
        .required('Quantity is required')
        .min(1, 'Quantity must be at least 1')
        .test('max-available', 'Quantity cannot exceed available stock', function (value) {
          const { product_id: productId } = this.parent
          if (!productId || !value) {
            return true
          }

          const availableQty = Number.parseInt(getAvailableQuantity(productId), 10)
          
          return value <= availableQty
        })
        .label('Quantity'),
      unit_price: yup.number().required('Unit Price is required').min(0, 'Unit Price must be at least 0').label('Unit Price'),
      description: yup.string().nullable(),
    }),
  ),
})

// Form Setup
const { handleSubmit, errors, values, defineField } = useForm({
  validationSchema: schema,
  initialValues: {
    transfer_date: '',
    source_store_id: parsedUser.store_id || 0, // Set default to user's store
    source_branch_id: branch.value.id,
    destination_branch_id: 0,
    destination_store_id: 0,
    items: [],
  },
})

const [transfer_date] = defineField('transfer_date')
const [sourceStoreId] = defineField('source_store_id')
const [sourceBranchId] = defineField('source_branch_id')
const [destinationBranchId] = defineField('destination_branch_id')
const [destinationStoreId] = defineField('destination_store_id')
const { fields, push, remove } = useFieldArray('items')

// Data
const stores = ref<Store[]>([])
const branches = ref<Branch[]>([])
const products = ref<GenericSetUp[]>([])

// Fetch stores by branch
const fetchStoresByBranch = async (selectedBranchId: number) => {
  try {
    const response = await makeApiCall<Store[]>('GET', `/my-stores/${selectedBranchId}`)
    stores.value = response.data
  }
  catch (error) {
    console.error('Error fetching stores for branch:', error)
    stores.value = []
  }
}

// Watch for source store changes to load products
watch(sourceStoreId, async (newStoreId) => {
  if (newStoreId && newStoreId > 0) {
    await fetchProducts(newStoreId)
    // Clear existing items when source store changes
    if (fields.value.length > 0) {
      fields.value.forEach((field, index) => {
        field.value.product_id = 0
        field.value.quantity = 1
        field.value.quantity_pieces = 1
        field.value.unit_price = 0
        field.value.description = ''
      })
    }
  } else {
    products.value = []
  }
})

// Watch for destination branch changes to update stores
watch(destinationBranchId, async newBranchId => {
  if (newBranchId && newBranchId > 0) {
    // Clear the current store selection
    destinationStoreId.value = 0
    // Fetch stores for the selected branch
    await fetchStoresByBranch(newBranchId)
  } else {
    // Clear stores if no branch is selected
    stores.value = []
    destinationStoreId.value = 0
  }
})

// Watch for product changes to trigger quantity validation
watch(() => values.items, (newItems) => {
  if (newItems && newItems.length > 0) {
    // Trigger validation for all items when any product changes
    newItems.forEach((item, index) => {
      if (item.product_id) {
        // Trigger validation for the quantity field
        const quantityField = `items[${index}].quantity`
        if (errors.value[quantityField]) {
          // Force re-validation
          handleSubmit(() => {})()
        }
      }
    })
  }
}, { deep: true })

// Watch for product changes to auto-fill unit price
watch(() => values.items, (newItems) => {
  if (newItems && newItems.length > 0) {
    newItems.forEach((item, index) => {
      if (item.product_id && !item.unit_price) {
        // Auto-fill unit price if not already set
        getUnitPrice(item.product_id, index)
      }
    })
  }
}, { deep: true })

// Watch for individual product_id changes to auto-fill unit price
watch(() => fields.value, (newFields) => {
  if (newFields && newFields.length > 0) {
    newFields.forEach((field, index) => {
      if (field.value.product_id && !field.value.unit_price) {
        // Auto-fill unit price when product is selected
        getUnitPrice(field.value.product_id, index)
      }
    })
  }
}, { deep: true })

// Watch for quantity changes to update quantity_pieces
watch(() => fields.value, (newFields) => {
  if (newFields && newFields.length > 0) {
    newFields.forEach((field, index) => {
      if (field.value.product_id && field.value.quantity) {
        // Calculate quantity_pieces when quantity changes
        updateQuantityPieces(field.value.product_id, field.value.quantity, index)
      }
    })
  }
}, { deep: true })

// Add an Item
const addItem = () => {
  push({
    product_id: 0,
    quantity: 1, // Default quantity to 1 for clarity
    quantity_pieces: 1, // Default quantity to 1 for clarity
    unit_price: 0,
    description: '',
  })
}

const removeItem = (index: number) => {
  remove(index)
}

const nextStep = () => {
  if (step.value < 3)
    step.value++
}

const prevStep = () => {
  if (step.value > 1)
    step.value--
}

// Submit Handler
const submit = handleSubmit(data => {
  console.log('Form submitted:', data)
  nextStep()
})

const saveRequest = async () => {
  isLoading.value = true
  try {
    console.log('Form submitted:', values)
    const response = await makeApiCall<Record<string, any>>('POST', '/store-transfer-orders', values)
    status.value = response.data.source_status
    isLoading.value = false
    nextStep()
  }
  catch (error) {
    console.error('Error creating transfer order:', error)
    isLoading.value = false
    //prevStep()
  }
}

// Updated fetchProducts function to use the new API endpoint
const fetchProducts = async (storeId: number) => {
  try {
    const response = await makeApiCall<Product[]>('GET', `/store-items/${storeId}/products`)
    products.value = response.data
  } catch (error) {
    console.error('Error fetching products:', error)
    products.value = []
  }
}

const fetchSourceStores = async () => {
  try {
    const response = await makeApiCall<Store[]>('GET', `/my-stores/${branch.value.id}`)
    stores.value = response.data
    // Set default source store if user has one
    if (parsedUser.store_id) {
      sourceStoreId.value = parsedUser.store_id
    }
  } catch (error) {
    console.error('Error fetching source stores:', error)
    stores.value = []
  }
}

const fetchBranches = async () => {
  try {
    const response = await makeApiCall<Branch[]>('GET', '/branches')
    branches.value = response.data
  }
  catch (error) {
    console.error('Error fetching branches:', error)
  }
}

function getItemValue(id: any, data: any) {
  const item = data.find((dataItem: any) => dataItem?.id === id)
  return item ? item.name : 'N/A'
}

function getAvailableQuantity(productId: number) {
  if (!productId || productId === 0) {
    return '0'
  }

  const product = products.value.find((item: any) => item.id === productId)
  return product ? (product?.storeItems[0]?.quantity_request / product?.storeItems[0]?.quantity_in_package || '0').toString() : '0'
}

function getUnitPrice(productId: number, index: number) {
  if (!productId || productId === 0) {
    return 0
  }

  const product = products.value.find((item: any) => item.id === productId)
  const unitPrice = product?.storeItems?.[0]?.cost_price || 0
  
  // Set the unit price in the form field
  if (fields.value[index]) {
    fields.value[index].value.unit_price = unitPrice
  }
  
  return unitPrice
}

function updateQuantityPieces(productId: number, quantity: number, index: number) {
  if (!productId || productId === 0 || !quantity || quantity <= 0) {
    return 0
  }

  const product = products.value.find((item: any) => item.id === productId)
  const quantityInPackage = product?.storeItems?.[0]?.quantity_in_package || 1

  const quantityPieces = quantity * quantityInPackage

  // Set the quantity_pieces in the form field
  if (fields.value[index]) {
    fields.value[index].value.quantity_pieces = quantityPieces
  }

  return quantityPieces
}

function calculateTotalAmount() {
  if (!values.items || values.items.length === 0) {
    return 0
  }

  return values.items.reduce((total, item) => {
    const itemTotal = (item.quantity || 0) * (item.unit_price || 0)
    return total + itemTotal
  }, 0)
}

// Computed property to check if form is valid for proceeding
const isFormValid = computed(() => {
  // Check basic required fields
  if (!values.transfer_date || !values.source_store_id || !values.destination_store_id || !values.destination_branch_id) {
    return false
  }

  // Check if there are any items
  if (!values.items || values.items.length === 0) {
    return false
  }

  // Check if all items have required fields
  return values.items.every(item => 
    item.product_id && 
    item.quantity && 
    item.quantity > 0 && 
    item.unit_price && 
    item.unit_price > 0
  )
})

// Fetch Data
onMounted(async () => {
  if (values.items.length === 0) {
    push({
      product_id: 0,
      quantity: 1,
      quantity_pieces: 1,
      unit_price: 0,
      description: '',
    })
  }
  try {
    await Promise.all([
      fetchSourceStores(),
      fetchBranches(),
    ])
    console.log(stores.value)
  }
  catch (error) {
    console.error('Error during data fetch:', error)
  }
})
</script>

<template>
  <VCard>
    <VCardTitle>Create Stock Transfer Request</VCardTitle>
    <VCardText>
      <VForm @submit="submit">
        <VStepper v-model="step" :alt-label>
          <VStepperHeader color="primary" elevation="3" rounded>
            <VStepperStep 
              :complete="step > 1" 
              step="1" 
              editable 
              complete-icon="ri-check-circle"
              error-icon="ri-alert-circle" 
              color="green"
            >
              Request Information
            </VStepperStep>
            <VDivider />
            <VStepperStep 
              :complete="step > 2" 
              step="2" 
              editable 
              complete-icon="ri-check-circle" 
              color="blue"
              error-icon="ri-alert-circle"
            >
              Confirmation
            </VStepperStep>
            <VDivider />
            <VStepperStep 
              step="3" 
              editable 
              complete-icon="ri-check-circle" 
              color="green"
              error-icon="ri-alert-circle"
            >
              Completion
            </VStepperStep>
          </VStepperHeader>
          <br/>

          <VStepperItems>
            <VStepperContent v-if="step === 1" step="1">
              <VRow>
                <VCol cols="12" md="6">
                  <VTextField 
                    v-model="transfer_date" 
                    label="Request Date" 
                    type="date"
                    :max="new Date().toISOString().split('T')[0]"
                    :error-messages="errors?.transfer_date" 
                    required 
                  />
                </VCol>
                
                <VCol cols="12" md="6">
                  <VTextField label="Origin Branch" v-model="branch.name" readonly />
                </VCol>
                
                <VCol cols="12" md="6">
                  <VSelect 
                    v-model="sourceStoreId" 
                    label="Source Store" 
                    :items="stores" 
                    item-value="id" 
                    item-title="name"
                    :error-messages="errors?.source_store_id" 
                    required 
                  />
                </VCol>
  
                <VCol cols="12" md="6">
                  <VSelect 
                    v-model="destinationBranchId" 
                    label="Destination Branch" 
                    :items="branches" 
                    item-value="id" 
                    item-title="name"
                    :error-messages="errors?.destination_branch_id" 
                    required 
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VSelect 
                    v-model="destinationStoreId" 
                    label="Destination Store" 
                    :items="stores" 
                    item-value="id" 
                    item-title="name"
                    :error-messages="errors?.destination_store_id" 
                    required 
                  />
                </VCol>
              </VRow>
              <br>
              <!-- Items Section -->
              <VDivider />
              <VCardSubtitle><b>Items for Transfer</b></VCardSubtitle>
              <VDivider />
              <br>

              <VRow v-for="(item, index) in fields" :key="index">
                <VCol cols="12" md="3">
                  <VSelect 
                    v-model="item.value.product_id" 
                    label="Product" 
                    :items="products" 
                    item-value="id"
                    item-title="name" 
                    :error-messages="errors?.[`items[${index}].product_id`]" 
                    required 
                  />
                </VCol>
                <VCol cols="12" md="2">
                  <VTextField 
                    :model-value="getAvailableQuantity(item.value.product_id)"
                    label="Available Qty" 
                    readonly
                    variant="outlined"
                    color="info"
                  />
                </VCol>
                <VCol cols="12" md="2">
                  <VTextField 
                    v-model="item.value.quantity" 
                    label="Quantity" 
                    type="number"
                    :error-messages="errors?.[`items[${index}].quantity`]" 
                    required 
                  />
                </VCol>
                <VCol cols="12" md="2">
                  <VTextField 
                    v-model="item.value.unit_price" 
                    label="Unit Price" 
                    type="number"
                    readonly
                    variant="outlined"
                    color="info"
                    :error-messages="errors?.[`items[${index}].unit_price`]" 
                    required 
                  />
                </VCol>
                
                <VCol cols="12" md="2">
                  <VTextField 
                    v-model="item.value.description" 
                    label="Description"
                    :error-messages="errors?.[`items[${index}].description`]" 
                  />
                </VCol>
                <VCol cols="12" md="1" class="d-flex align-center">
                  <VBtn icon @click="removeItem(index)">
                    <VIcon icon="ri-delete-bin-line" />
                  </VBtn>
                </VCol>
              </VRow>

              <VBtn color="primary" class="mt-4" @click="addItem">
                Add Item
              </VBtn>
              <VBtn 
                color="success" 
                class="mt-4 pull-right" 
                type="submit" 
                :disabled="!isFormValid"
                :loading="isLoading"
              >
                Proceed
              </VBtn>
            </VStepperContent>
            
            <VStepperContent v-if="step === 2" step="2">
              <VList dense>
                <VRow>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Transfer Date
                        </VListItemTitle>
                        <VListItemSubtitle>{{ values.transfer_date }}</VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>

                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Origin Branch
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(sourceBranchId, branches) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Origin Store
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(sourceStoreId, stores) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Destination Branch
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(destinationBranchId, branches) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Destination Store
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(destinationStoreId, stores) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                </VRow>
              </VList>
              <!-- Loop through journalDetails and display each entry -->
              <VDivider />
              <VTable class="product-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price (N)</th>
                    <th>Total</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(entry, index) in fields" :key="index">
                    <td>
                      {{ index + 1 }}. {{ getItemValue(entry.value.product_id, products) }}
                    </td>
                    <td>{{ entry.value.quantity }}</td>
                    <td text-right>
                      {{ formatNumber(entry.value.unit_price) }}
                    </td>
                    <td text-right>
                      {{ formatNumber(entry.value.quantity * entry.value.unit_price) }}
                    </td>
                    <td text-right>
                      {{ entry.value.description }}
                    </td>
                  </tr>
                  <tr class="font-weight-bold">
                    <td colspan="3" class="text-right"><strong>Sum of Total:</strong></td>
                    <td class="text-right">
                      <strong>{{ formatNumber(calculateTotalAmount()) }}</strong>
                    </td>
                    <td></td>
                  </tr>
                </tbody>
              </VTable>

              <VBtn color="primary" class="mt-4" @click="prevStep">
                Back
              </VBtn>
              <VBtn color="success" class="mt-4" :loading="isLoading" :disabled="isLoading" @click="saveRequest">
                Submit
              </VBtn>
            </VStepperContent>
            
            <VStepperContent v-if="step === 3" step="3">
              <VList dense>
                <VRow>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Transfer Date
                        </VListItemTitle>
                        <VListItemSubtitle>{{ values.transfer_date }}</VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>

                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Origin Branch
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(sourceBranchId, branches) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Origin Store
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(sourceStoreId, stores) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Destination Branch
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(destinationBranchId, branches) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Destination Store
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ getItemValue(destinationStoreId, stores) }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                  <VCol cols="12" md="6">
                    <VListItem>
                      <VListItemContent class="d-flex justify-between align-center">
                        <VListItemTitle class="mr-2">
                          Status
                        </VListItemTitle>
                        <VListItemSubtitle>
                          {{ status }}
                        </VListItemSubtitle>
                      </VListItemContent>
                    </VListItem>
                  </VCol>
                </VRow>
              </VList>
              <!-- Loop through journalDetails and display each entry -->
              <VDivider />
              <VTable class="product-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price (N)</th>
                    <th>Total</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(entry, index) in fields" :key="index">
                    <td>
                      {{ index + 1 }}. {{ getItemValue(entry.value.product_id, products) }}
                    </td>
                    <td>{{ entry.value.quantity }}</td>
                    <td text-right>
                      {{ formatNumber(entry.value.unit_price) }}
                    </td>
                    <td text-right>
                      {{ formatNumber(entry.value.quantity * entry.value.unit_price) }}
                    </td>
                    <td text-right>
                      {{ entry.value.description }}
                    </td>
                  </tr>
                  <tr class="font-weight-bold">
                    <td colspan="3" class="text-right"><strong>Sum of Total:</strong></td>
                    <td class="text-right">
                      <strong>{{ formatNumber(calculateTotalAmount()) }}</strong>
                    </td>
                    <td></td>
                  </tr>
                </tbody>
              </VTable>
            </VStepperContent>
          </VStepperItems>
        </VStepper>
      </VForm>
    </VCardText>
  </VCard>
</template>

<style scoped>
/* Add custom styles here if needed */
</style> 